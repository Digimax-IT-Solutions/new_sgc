<?php

require_once __DIR__ . '/../../_init.php';
class Service
{
  public $id;
  public $procedure_name;
  public $price;
  public $description;
  public $machine;
  public $process;
  public $recommended_for;
  public $results;
  public $code;
  public $category;

  public function __construct($service)
  {
    $this->id = $service['service_id']; // Assuming 'procedure_id' is the primary key
    $this->procedure_name = $service['procedure_name'];
    $this->price = $service['price'];
    $this->description = $service['description'];
    $this->machine = $service['machine'];
    $this->process = $service['process'];
    $this->recommended_for = $service['recommended_for'];
    $this->results = $service['results'];
    $this->code = $service['code'];
    $this->category = $service['category_name'];
  }


  public static function all()
  {
    global $connection;

    $stmt = $connection->prepare('SELECT s.*, c.name AS category_name FROM services s INNER JOIN categories c ON s.category_id = c.id');

    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $result = $stmt->fetchAll();
    $result = array_map(fn($service) => new Service($service), $result);

    return $result;
  }

  public static function add(
    $procedure_name,
    $price,
    $description,
    $machine,
    $process,
    $recommended_for,
    $results,
    $code,
    $category_id
  ) {
    global $connection;

    $sql_command = 'INSERT INTO services (
      procedure_name,
      price,
      description,
      machine,
      process,
      recommended_for,
      results,
      code,
      category_id
    ) 
        VALUES (
          :procedure_name,
          :price,
          :description,
          :machine,
          :process,
          :recommended_for,
          :results,
          :code,
          :category_id
        )';

    $stmt = $connection->prepare($sql_command);
    $stmt->bindParam(':procedure_name', $procedure_name);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':machine', $machine);
    $stmt->bindParam(':process', $process);
    $stmt->bindParam(':recommended_for', $recommended_for);
    $stmt->bindParam(':results', $results);
    $stmt->bindParam(':code', $code);
    $stmt->bindParam(':category_id', $category_id);

    $stmt->execute();
  }

  // public function update()
  // {
  //   global $connection;

  //   $sql_command = 'UPDATE items SET 
  //                       vendor_id = :preferredVendorId,
  //                       category_id = :category_id,
  //                       uom_id = :uom_id,
  //                       itemcogsaccount_id = :itemCogsAccountId,
  //                       itemincomeaccount_id = :itemIncomeAccountId,
  //                       itemassetsaccount_id = :itemAssetsAccountId,
  //                       item_name = :name,
  //                       item_code = :itemCode,
  //                       item_type = :itemType,
  //                       reorder_point = :reorderPoint,
  //                       sales_description = :salesDescription,
  //                       selling_price = :price,
  //                       purchase_description = :purchaseDescription,
  //                       cost_price = :costPrice,
  //                       quantity = :quantity
  //                       WHERE id = :id';

  //   $stmt = $connection->prepare($sql_command);
  //   $stmt->bindParam(':preferredVendorId', $this->preferredVendorId);
  //   $stmt->bindParam(':category_id', $this->category_id);
  //   $stmt->bindParam(':uom_id', $this->uom_id);
  //   $stmt->bindParam(':itemCogsAccountId', $this->itemCogsAccountId);
  //   $stmt->bindParam(':itemIncomeAccountId', $this->itemIncomeAccountId);
  //   $stmt->bindParam(':itemAssetsAccountId', $this->itemAssetsAccountId);
  //   $stmt->bindParam(':name', $this->name);
  //   $stmt->bindParam(':itemCode', $this->itemCode);
  //   $stmt->bindParam(':itemType', $this->itemType); // Corrected parameter name
  //   $stmt->bindParam(':reorderPoint', $this->reorderPoint); // Corrected parameter name
  //   $stmt->bindParam(':salesDescription', $this->salesDescription);
  //   $stmt->bindParam(':price', $this->price);
  //   $stmt->bindParam(':purchaseDescription', $this->purchaseDescription);
  //   $stmt->bindParam(':costPrice', $this->costPrice);
  //   $stmt->bindParam(':quantity', $this->quantity);
  //   $stmt->bindParam(':id', $this->id);

  //   $stmt->execute();
  // }

  public function delete()
  {
    global $connection;

    $stmt = $connection->prepare('DELETE FROM `services` WHERE service_id=:id');
    $stmt->bindParam('id', $this->id);
    $stmt->execute();
  }

  // private function getCategory($product)
  // {
  //   if (isset($product['category_name'])) {
  //     return new Category([
  //       'id' => $product['category_id'],
  //       'name' => $product['category_name']
  //     ]);
  //   }

  //   return Category::find($product['category_id']);
  // }


  public static function find($id)
  {
    global $connection;

    $stmt = $connection->prepare('SELECT s.*, c.name AS category_name FROM services s INNER JOIN categories c ON s.category_id = c.id WHERE s.service_id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    $result = $stmt->fetch();

    if ($result) {
      return new Service($result);
    }

    return null;
  }
}