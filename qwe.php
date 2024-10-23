<?php

class Database
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = new PDO("mysql:host=localhost;dbname=laperla", "root", "digimax2023");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    public function getTables()
    {
        $stmt = $this->pdo->query("SHOW TABLES");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function clearTable($tableName)
    {
        $stmt = $this->pdo->prepare("TRUNCATE TABLE " . $tableName);
        return $stmt->execute();
    }

    public function optimizeTable($tableName)
    {
        $stmt = $this->pdo->prepare("OPTIMIZE TABLE " . $tableName);
        return $stmt->execute();
    }

    public function backupDatabase($tables = '*')
    {
        $output = '';
        if ($tables == '*') {
            $tables = $this->getTables();
        } else {
            $tables = is_array($tables) ? $tables : explode(',', $tables);
        }

        // Disable foreign key checks
        $output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $result = $this->pdo->query("SELECT * FROM $table");
            $numFields = $result->columnCount();

            $output .= "DROP TABLE IF EXISTS `$table`;\n";
            $row2 = $this->pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_NUM);
            $output .= $row2[1] . ";\n\n";

            while ($row = $result->fetch(PDO::FETCH_NUM)) {
                $output .= "INSERT INTO `$table` VALUES(";
                for ($j = 0; $j < $numFields; $j++) {
                    if (isset($row[$j])) {
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = str_replace("\n", "\\n", $row[$j]);
                        $output .= '"' . $row[$j] . '"';
                    } else {
                        $output .= 'NULL';
                    }
                    if ($j < ($numFields - 1)) {
                        $output .= ',';
                    }
                }
                $output .= ");\n";
            }
            $output .= "\n";
        }

        // Re-enable foreign key checks
        $output .= "\nSET FOREIGN_KEY_CHECKS=1;\n\n";

        // Add triggers
        $output .= $this->backupTriggers();

        // Add stored procedures and functions
        $output .= $this->backupRoutines();

        return $output;
    }

    private function backupTriggers()
    {
        $output = "-- Triggers\n";
        $triggers = $this->pdo->query("SHOW TRIGGERS")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($triggers as $trigger) {
            $output .= "\nDROP TRIGGER IF EXISTS `" . $trigger['Trigger'] . "`;\n";
            $output .= "DELIMITER //\n";
            $output .= "CREATE TRIGGER `" . $trigger['Trigger'] . "` " . $trigger['Timing'] . " " . $trigger['Event'] . " ON `" . $trigger['Table'] . "`\n";
            $output .= "FOR EACH ROW\n";
            $output .= $trigger['Statement'] . "\n";
            $output .= "//\nDELIMITER ;\n\n";
        }
        return $output;
    }

    private function backupRoutines()
    {
        $output = "-- Stored Procedures and Functions\n";
        $routines = $this->pdo->query("SHOW PROCEDURE STATUS WHERE Db = DATABASE()")->fetchAll(PDO::FETCH_ASSOC);
        $routines = array_merge($routines, $this->pdo->query("SHOW FUNCTION STATUS WHERE Db = DATABASE()")->fetchAll(PDO::FETCH_ASSOC));

        foreach ($routines as $routine) {
            $type = $routine['Type'];
            $name = $routine['Name'];
            $createStatement = $this->pdo->query("SHOW CREATE $type `$name`")->fetch(PDO::FETCH_ASSOC);
            $output .= "\nDROP $type IF EXISTS `$name`;\n";
            $output .= "DELIMITER //\n";
            $output .= $createStatement['Create ' . ucfirst(strtolower($type))] . "\n";
            $output .= "//\nDELIMITER ;\n\n";
        }
        return $output;
    }
}


// Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('database');

// Database connection
$db = new Database(); // Assume you have a Database class for handling connections

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'backup') {
            $backup = $db->backupDatabase();
            $filename = 'database_backup_' . date('Y-m-d_H-i-s') . '.sql';
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary");
            header("Content-disposition: attachment; filename=\"" . $filename . "\"");
            echo $backup;
            exit;
        } elseif (isset($_POST['selected_tables'])) {
            // ... existing clear and optimize code ...
            $selectedTables = $_POST['selected_tables'];
            $action = $_POST['action'];
            $messages = [];

            foreach ($selectedTables as $tableName) {
                if ($action === 'clear') {
                    $db->clearTable($tableName);
                    $messages[] = "Table '$tableName' has been cleared.";
                } elseif ($action === 'optimize') {
                    $db->optimizeTable($tableName);
                    $messages[] = "Table '$tableName' has been optimized.";
                }
            }

            $message = implode('<br>', $messages);
        }
    }
}

// // Handle form submissions
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     if (isset($_POST['action']) && isset($_POST['selected_tables'])) {

//     }
// }

// Get list of tables
$tables = $db->getTables();
?>

<?php require 'views/templates/header.php'; ?>
<?php require 'views/templates/sidebar.php'; ?>

<div class="main">
    <?php require 'views/templates/navbar.php'; ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-sm-12 d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-3"><strong>Database Management</strong></h1>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">

                            <!-- Warning Alert -->
                            <div class="alert alert-danger" role="alert">
                                <strong>Warning:</strong> This section is critical. Please proceed with caution when
                                making changes to the database.
                            </div>
                            <?php if (isset($message)): ?>
                                <div class="alert alert-success"><?php echo $message; ?></div>
                            <?php endif; ?>

                            <h5>Database Tables</h5>
                            <form method="post">
                                <div class="row g-0">
                                    <div class="col-12 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                            <label class="form-check-label" for="selectAll">
                                                Select All
                                            </label>
                                        </div>
                                    </div>
                                    <?php foreach ($tables as $index => $table): ?>
                                        <?php if ($index % 2 == 0)
                                            echo '<div class="row g-0">'; ?>
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="selected_tables[]"
                                                    value="<?php echo htmlspecialchars($table); ?>"
                                                    id="table<?php echo $index; ?>">
                                                <label class="form-check-label" for="table<?php echo $index; ?>">
                                                    <?php echo htmlspecialchars($table); ?>
                                                </label>
                                            </div>
                                        </div>
                                        <?php if ($index % 2 == 1 || $index == count($tables) - 1)
                                            echo '</div>'; ?>
                                    <?php endforeach; ?>
                                </div>
                                <div class="mt-3">
                                    <div class="mt-3">
                                        <button type="submit" name="action" value="clear" class="btn btn-warning btn-sm"
                                            onclick="return confirm('Are you sure you want to clear the selected tables?')">Clear
                                            Selected Tables</button>
                                        <button type="submit" name="action" value="optimize"
                                            class="btn btn-info btn-sm">Optimize Selected Tables</button>
                                        <button type="submit" name="action" value="backup"
                                            class="btn btn-success btn-sm">Backup Database</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    document.getElementById('selectAll').onclick = function() {
        var checkboxes = document.getElementsByName('selected_tables[]');
        for (var checkbox of checkboxes) {
            checkbox.checked = this.checked;
        }
    }
</script>

<?php require 'views/templates/footer.php'; ?>