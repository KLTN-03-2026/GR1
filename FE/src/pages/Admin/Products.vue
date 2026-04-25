<template>
  <div class="products-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-2">Products</h1>
        <p class="text-muted">Manage all products</p>
      </div>
      <button class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add New Product
      </button>
    </div>

    <div class="row">
      <div v-for="product in products" :key="product.id" class="col-md-4 mb-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h5 class="card-title">{{ product.name }}</h5>
            <p class="card-text text-muted small">{{ product.description }}</p>
            <div class="d-flex justify-content-between align-items-center mt-3">
              <span class="h5 mb-0">${{ product.price }}</span>
              <span v-if="product.stock > 0" class="badge bg-success">Stock: {{ product.stock }}</span>
              <span v-else class="badge bg-danger">Out of Stock</span>
            </div>
          </div>
          <div class="card-footer bg-white border-top">
            <button class="btn btn-sm btn-outline-primary me-2">Edit</button>
            <button class="btn btn-sm btn-outline-danger">Delete</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Products',
  data() {
    return {
      products: [
        { id: 1, name: 'Product 1', description: 'High quality product', price: 99.99, stock: 10 },
        { id: 2, name: 'Product 2', description: 'Best seller item', price: 149.99, stock: 5 },
        { id: 3, name: 'Product 3', description: 'Limited edition', price: 199.99, stock: 0 },
      ]
    };
  }
};
</script>

<style scoped>
.products-page {
  animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
}
</style>
