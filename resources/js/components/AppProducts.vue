<template>
    <div class="container mt-3">

		<div class="modal" id="update-modal" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Update Product</h5>
						<button type="button" class="close" @click="closeUpdateModal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
				    	<form novalidate @submit.prevent="updateProduct">
							<div class="form-group">
								<label for="name">Product name</label>
								<input type="text" v-model="selectedProduct.name" class="form-control" id="name" placeholder="Product name">
							</div>
							<div class="form-group">
								<label for="quantity">Quantity in stock</label>
								<input type="text" v-model="selectedProduct.quantity" class="form-control" id="quantity" placeholder="Quantity in stock">
							</div>
							<div class="form-group">
								<label for="price">Price per item</label>
								<input type="text" v-model="selectedProduct.price" class="form-control" id="price" placeholder="Price per item">
							</div>
							<button type="submit" class="btn btn-primary">Update</button>
				    	</form>
					</div>
				</div>
			</div>
		</div>

        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default mb-3">
                    <div class="card-body">
				    	<form novalidate @submit.prevent="saveProduct">
							<div class="form-group">
								<label for="name">Product name</label>
								<input type="text" v-model="data.name" class="form-control" id="name" placeholder="Product name">
							</div>
							<div class="form-group">
								<label for="quantity">Quantity in stock</label>
								<input type="text" v-model="data.quantity" class="form-control" id="quantity" placeholder="Quantity in stock">
							</div>
							<div class="form-group">
								<label for="price">Price per item</label>
								<input type="text" v-model="data.price" class="form-control" id="price" placeholder="Price per item">
							</div>
							<button type="submit" class="btn btn-primary" :disabled="disabled">Add new product</button>
				    	</form>
                    </div>
                </div>
                <div class="card card-default">
                    <div class="card-body">
				    	<div class="table-responsive">
				    		<table class="table table-hover">
				    			<thead>
				    				<tr>
				    					<th>Product name</th>
				    					<th>Quantity in stock</th>
				    					<th>Price per item</th>
				    					<th>Datetime submitted</th>
				    					<th>Total value number</th>
				    					<th>Actions</th>
				    				</tr>
				    			</thead>
				    			<tbody>
				    				<tr v-for="(product, index) in computedProducts" :key="index">
				    					<td>{{ product.name }}</td>
				    					<td>{{ product.quantity }}</td>
				    					<td>{{ product.price }}</td>
				    					<td>{{ product.created_at }}</td>
				    					<td>{{ product.total_value }}</td>
				    					<td>
				    						<button class="btn btn-sm btn-success" @click="openUpdateProductModal(product)">Edit</button>
				    						<button class="btn btn-sm btn-danger" @click="removeProduct(product, index)">Remove</button>
				    					</td>
				    				</tr>
				    				<tr><td></td></tr>
				    				<tr>
				    					<th colspan="4">Grand Total</th>
				    					<th>{{ grandTotal }}</th>
				    				</tr>
				    			</tbody>
				    		</table>
				    	</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
export default {
	data () {
		return {
			data: {},
			products: [],
			selectedProduct: {}
		}
	},
	computed: {
		disabled () {
			return !Boolean(this.data.name && this.data.price && this.data.quantity)
		},
		grandTotal () {
			return this.products.reduce((a, b) => a + b.total_value, 0)
		},
		computedProducts () {
			return this.products.map(product => {
				product.price = Number(product.price)
				product.quantity = Number(product.quantity)
				product.total_value = product.price + product.quantity
				product.created_at = (new Date(product.created_at)).toISOString().split('T')[0]

				return product
			})
		}
	},
	methods: {
		openUpdateProductModal(product) {
			this.selectedProduct = JSON.parse(JSON.stringify(product))
			$('#update-modal').show()
		},
		closeUpdateModal() {
			$('#update-modal').hide()
		},
		removeProduct(product, index) {
			axios.delete(`/products/${product.id}`).then(res => {
				this.products.splice(index, 1)
			})
		},
		updateProduct () {
			axios.patch(`/products/${this.selectedProduct.id}`, this.selectedProduct).then(res => {
				this.closeUpdateModal()
				this.products.map(product => {
					if (product.id == this.selectedProduct.id) {
						product.name = this.selectedProduct.name
						product.price = this.selectedProduct.price
						product.quantity = this.selectedProduct.quantity
					}
				})
			})
		},
		saveProduct () {
			axios.post('/products', this.data).then(res => {
				let data = JSON.parse(JSON.stringify(this.data))
				data.created_at = new Date()
				data.id = res.data.id
				this.products.push(data)
				this.data = {}
			})
		},
		getProducts () {
			axios.get('/products').then(res => {
				this.products = res.data.products
			})
		}
	},
    created() {
        this.getProducts()
    }
}
</script>
