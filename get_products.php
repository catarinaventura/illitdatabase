<?php
include "db.php";

header("Content-Type: application/json");

$products = [];
$categories = [];

$sql = "SELECT * FROM products WHERE is_active = 1";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {

    if (!isset($categories[$row["category"]])) {
        $categories[$row["category"]] = [
            "id" => $row["category"],
            "name" => ucfirst($row["category"])
        ];
    }

    $products[] = [
        "id" => $row["id"],
        "name" => $row["name"],
        "category" => $row["category"],
        "description" => $row["description"],
        "price" => (float)$row["price"],
        "stock" => (int)$row["stock"],
        "type" => ucfirst($row["category"]),
        "images" => [$row["image"] ?: "images/placeholder.jpg"]
    ];
}

echo json_encode([
    "categories" => array_values($categories),
    "products" => $products
]);

