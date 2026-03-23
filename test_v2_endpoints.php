<?php
include "vendor/autoload.php";

use IS\PazarYeri\Trendyol\TrendyolClient;

$trendyol = new TrendyolClient(); 
$trendyol->setSupplierId(168599);
$trendyol->setUsername("JVyxBBJvH1bDnyDvjG2o");
$trendyol->setPassword("pdPOVriVk0CjntTHSyU9");

echo "1. Testing Category Attributes V2\n";
// Let's use a random category id to test, e.g. 14609 (from docs) or 411
$categoryId = 411;
$categoryAttributes = $trendyol->category->getCategoryAttributesV2($categoryId);
echo "RESPONSE:\n";
print_r($categoryAttributes);

if (isset($categoryAttributes->categoryAttributes) && count($categoryAttributes->categoryAttributes) > 0) {
    echo "SUCCESS: getCategoryAttributesV2\n";
    $attributeId = $categoryAttributes->categoryAttributes[0]->attribute->id;
    
    echo "\n2. Testing Category Attribute Values V2\n";
    $attributeValues = $trendyol->category->getCategoryAttributeValuesV2($categoryId, $attributeId);
    echo "RESPONSE:\n";
    print_r($attributeValues);
    if (isset($attributeValues->content)) {
        echo "SUCCESS: getCategoryAttributeValuesV2\n";
    } else {
        echo "FAILED: getCategoryAttributeValuesV2\n";
    }
} else {
    echo "FAILED: getCategoryAttributesV2\n";
}

echo "\n3. Testing Approved Products V2\n";
$approvedProducts = $trendyol->product->filterApprovedProductsV2(['size' => 1]);
echo "RESPONSE:\n";
print_r($approvedProducts);

if (isset($approvedProducts->content)) {
    echo "SUCCESS: filterApprovedProductsV2\n";
    
    if (count($approvedProducts->content) > 0) {
        $barcode = $approvedProducts->content[0]->variants[0]->barcode;
        echo "\n4. Testing Basic Product Filter V2\n";
        $basicProduct = $trendyol->product->filterProductsBasicV2($barcode);
        echo "RESPONSE:\n";
        print_r($basicProduct);
        if (isset($basicProduct->barcode)) {
            echo "SUCCESS: filterProductsBasicV2\n";
        } else {
            echo "FAILED: filterProductsBasicV2\n";
        }
    } else {
        echo "WARNING: No approved products found to test basic product filter.\n";
    }
} else {
    echo "FAILED: filterApprovedProductsV2\n";
}

echo "\n5. Testing Unapproved Products V2\n";
$unapprovedProducts = $trendyol->product->filterUnapprovedProductsV2(['size' => 1]);
echo "RESPONSE:\n";
print_r($unapprovedProducts);

if (isset($unapprovedProducts->content)) {
    echo "SUCCESS: filterUnapprovedProductsV2\n";
} else {
    echo "FAILED: filterUnapprovedProductsV2\n";
}
