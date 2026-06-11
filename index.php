<?php
switch ($action) {
        case 'add_product':
            $newProduct = [
                "id" => uniqid('prod_'),
                "name" => htmlspecialchars($input['name']),
                "price" => floatval($input['price']),
                "qty" => intval($input['qty'])
            ];
            $db['products'][] = $newProduct;
            saveDB($db);
            echo json_encode(["success" => true, "message" => "Product added successfully."]);
            break;

        // NEW: Update Action Handler
        case 'update_product':
            $productId = $input['id'] ?? '';
            $updated = false;

            foreach ($db['products'] as &$product) {
                if ($product['id'] === $productId) {
                    $product['name'] = htmlspecialchars($input['name']);
                    $product['price'] = floatval($input['price']);
                    $product['qty'] = intval($input['qty']);
                    $updated = true;
                    break;
                }
            }

            if ($updated) {
                saveDB($db);
                echo json_encode(["success" => true, "message" => "Product updated successfully."]);
            } else {
                echo json_encode(["success" => false, "message" => "Product target reference not found."]);
            }
            break;

        // FIXED: Deletion Request Handler
        case 'delete_product':
            $productId = $input['id'] ?? '';
            
            // Check if product array exists and filter it out
            if (isset($db['products'])) {
                $initialCount = count($db['products']);
                
                $db['products'] = array_values(array_filter($db['products'], function($p) use ($productId) {
                    return $p['id'] !== $productId;
                }));
                
                if (count($db['products']) < $initialCount) {
                    saveDB($db);
                    echo json_encode(["success" => true, "message" => "Product removed successfully."]);
                } else {
                    echo json_encode(["success" => false, "message" => "Product not found or already deleted."]);
                }
            } else {
                echo json_encode(["success" => false, "message" => "Inventory context empty."]);
            }
            break;

        // Other cases remain unchanged...
    php?>