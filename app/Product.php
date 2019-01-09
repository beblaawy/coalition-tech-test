<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
	public static function getFilePath () {
		return storage_path('app/products.json');
	}

	public static function getAllProducts () {
		if (!file_exists(self::getFilePath())) {
			file_put_contents(self::getFilePath(), '[]');
		}
		$products = json_decode(file_get_contents(self::getFilePath()), true);
		return collect($products ? $products : []);
	}

	public static function createNewProduct (array $product) {
		$products = self::getAllProducts();

        $products[] = $product;

        self::saveProducts($products);
	}

	public static function removeWhereId ($id) {
		$products = self::getAllProducts()->whereNotIn('id', [$id])->toArray();

        self::saveProducts($products);
	}

	public static function updateWhereId ($id, $data) {
		$products = self::getAllProducts();
		$product = $products->where('id', $id)->toArray();

		$products = $products->map(function($product) use ($id, $data) {
			if ($product['id'] == $id) {
				foreach ($data as $key => $value) {
					$product[$key] = $value;
				}
			}

			return $product;
		})->toArray();
		
		self::saveProducts($products);
	}

	public static function saveProducts ($products) {
        file_put_contents(self::getFilePath(), json_encode($products));
	}
}
