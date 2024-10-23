<?php

require_once __DIR__ . '/../../_init.php';
class Product
{
  public $id;
  public $item_name;
  public $item_code;
  public $item_type;
  public $item_vendor_id;
  public $item_uom_id;
  public $uom_name;
  public $item_reorder_point;
  public $item_category_id;
  public $item_quantity;
  public $item_sales_description;
  public $item_purchase_description;
  public $item_selling_price;
  public $item_cost_price;
  public $item_cogs_account_id;
  public $item_income_account_id;
  public $item_asset_account_id;

  public function __construct($product)
  {
    $this->id = $product['id'];
    $this->item_name = $product['item_name'];
    $this->item_code = $product['item_code'];
    $this->item_type = $product['item_type'];
    $this->item_vendor_id = $product['item_vendor_id'];
    $this->item_uom_id = $product['item_uom_id'];
    $this->uom_name = $product['uom_name'] ?? null;
    $this->item_reorder_point = $product['item_reorder_point'];
    $this->item_category_id = $product['item_category_id'];
    $this->item_quantity = $product['item_quantity'];
    $this->item_sales_description = $product['item_sales_description'];
    $this->item_purchase_description = $product['item_purchase_description'];
    $this->item_selling_price = $product['item_selling_price'];
    $this->item_cost_price = $product['item_cost_price'];
    $this->item_cogs_account_id = $product['item_cogs_account_id'];
    $this->item_income_account_id = $product['item_income_account_id'];
    $this->item_asset_account_id = $product['item_asset_account_id'];

  }



  public static function all()
  {
    global $connection;

    $stmt = $connection->prepare('
          SELECT items.*, uom.name AS uom_name
          FROM items
          LEFT JOIN uom ON items.item_uom_id = uom.id
      ');
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    $result = $stmt->fetchAll();

    $result = array_map(function ($item) {
      $product = new Product($item);
      $product->uom_name = $item['uom_name'];
      return $product;
    }, $result);

    return $result;
  }

  public static function add(
    $item_name,
    $item_code,
    $item_type,
    $item_vendor_id,
    $item_uom_id,
    $item_reorder_point,
    $item_category_id,
    $item_quantity,
    $item_sales_description,
    $item_purchase_description,
    $item_selling_price,
    $item_cost_price,
    $item_cogs_account_id,
    $item_income_account_id,
    $item_asset_account_id
  ) {
    global $connection;

    $sql_command = 'INSERT INTO items (
    item_name,
    item_code,
    item_type,
    item_vendor_id,
    item_uom_id,
    item_reorder_point,
    item_category_id,
    item_sales_description,
    item_purchase_description,
    item_selling_price,
    item_cost_price,
    item_cogs_account_id, 
    item_income_account_id, 
    item_asset_account_id
    ) 
        VALUES (
        :item_name,
        :item_code,
        :item_type,
        :item_vendor_id,
        :item_uom_id,
        :item_reorder_point,
        :item_category_id,
        :item_sales_description,
        :item_purchase_description,
        :item_selling_price,
        :item_cost_price,
        :item_cogs_account_id,
        :item_income_account_id,
        :item_asset_account_id 
        )';

    $stmt = $connection->prepare($sql_command);
    $stmt->bindParam(':item_name', $item_name);
    $stmt->bindParam(':item_code', $item_code);
    $stmt->bindParam(':item_type', $item_type);
    $stmt->bindParam(':item_vendor_id', $item_vendor_id);
    $stmt->bindParam(':item_uom_id', $item_uom_id);
    $stmt->bindParam(':item_reorder_point', $item_reorder_point);
    $stmt->bindParam(':item_category_id', $item_category_id);
    $stmt->bindParam(':item_sales_description', $item_sales_description);
    $stmt->bindParam(':item_purchase_description', $item_purchase_description);
    $stmt->bindParam(':item_selling_price', $item_selling_price);
    $stmt->bindParam(':item_cost_price', $item_cost_price);
    $stmt->bindParam(':item_cogs_account_id', $item_cogs_account_id);
    $stmt->bindParam(':item_income_account_id', $item_income_account_id);
    $stmt->bindParam(':item_asset_account_id', $item_asset_account_id);
    $stmt->execute();
  }


  public function delete()
  {
    global $connection;

    $stmt = $connection->prepare('DELETE FROM `items` WHERE id=:id');
    $stmt->bindParam('id', $this->id);
    $stmt->execute();
  }

  private function getCategory($product)
  {
    if (isset($product['category_name'])) {
      return new Category([
        'id' => $product['category_id'],
        'name' => $product['category_name']
      ]);
    }

    return Category::find($product['category_id']);
  }


  public static function find($id)
  {
      global $connection;
  
      // Explicitly list columns from 'items' table along with 'uom' name
      $stmt = $connection->prepare('
          SELECT 
              items.id, 
              items.item_name, 
              items.item_code, 
              items.item_type, 
              items.item_vendor_id, 
              items.item_uom_id, 
              items.item_reorder_point, 
              items.item_category_id, 
              items.item_quantity, 
              items.item_sales_description, 
              items.item_purchase_description, 
              items.item_selling_price, 
              items.item_cost_price, 
              items.item_cogs_account_id, 
              items.item_income_account_id, 
              items.item_asset_account_id, 
              uom.name AS uom_name
          FROM items
          LEFT JOIN uom ON items.item_uom_id = uom.id
          WHERE items.id = :id
      ');
  
      // Bind the parameter
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
  
      // Set fetch mode to associative array
      $stmt->setFetchMode(PDO::FETCH_ASSOC);
  
      // Fetch the result
      $result = $stmt->fetch();
  
      // Return result as Product object or null if not found
      if ($result) {
          return new Product($result);
      }
  
      return null;
  }
  

  public function update()
  {
      global $connection;
  
      $sql_command = '
          UPDATE items SET
              item_name = :item_name,
              item_code = :item_code,
              item_type = :item_type,
              item_vendor_id = :item_vendor_id,
              item_uom_id = :item_uom_id,
              item_reorder_point = :item_reorder_point,
              item_category_id = :item_category_id,
              item_quantity = :item_quantity,
              item_sales_description = :item_sales_description,
              item_purchase_description = :item_purchase_description,
              item_selling_price = :item_selling_price,
              item_cost_price = :item_cost_price,
              item_cogs_account_id = :item_cogs_account_id,
              item_income_account_id = :item_income_account_id,
              item_asset_account_id = :item_asset_account_id
          WHERE id = :id';
  
      $stmt = $connection->prepare($sql_command);
      $stmt->bindParam(':item_name', $this->item_name);
      $stmt->bindParam(':item_code', $this->item_code);
      $stmt->bindParam(':item_type', $this->item_type);
      $stmt->bindParam(':item_vendor_id', $this->item_vendor_id);
      $stmt->bindParam(':item_uom_id', $this->item_uom_id);
      $stmt->bindParam(':item_reorder_point', $this->item_reorder_point);
      $stmt->bindParam(':item_category_id', $this->item_category_id);
      $stmt->bindParam(':item_quantity', $this->item_quantity);
      $stmt->bindParam(':item_sales_description', $this->item_sales_description);
      $stmt->bindParam(':item_purchase_description', $this->item_purchase_description);
      $stmt->bindParam(':item_selling_price', $this->item_selling_price);
      $stmt->bindParam(':item_cost_price', $this->item_cost_price);
      $stmt->bindParam(':item_cogs_account_id', $this->item_cogs_account_id);
      $stmt->bindParam(':item_income_account_id', $this->item_income_account_id);
      $stmt->bindParam(':item_asset_account_id', $this->item_asset_account_id);
      $stmt->bindParam(':id', $this->id);
      $stmt->execute();
  }

}