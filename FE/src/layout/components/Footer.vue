<template>
  <footer class="footer-custom">
    <div class="container-fluid">
      <div class="row align-items-center">
        <!-- Left section -->
        <div class="col-md-6 text-start">
          <p class="mb-0">
            <span class="text-muted">&copy; 2024 MatDash Admin Dashboard.</span>
            <span class="text-secondary ms-1">All rights reserved.</span>
          </p>
        </div>

        <!-- Right section -->
        <div class="col-md-6 text-md-end">
          <ul class="footer-links">
            <li>
              <a href="#">Privacy Policy</a>
            </li>
            <li>
              <a href="#">Terms of Service</a>
            </li>
            <li>
              <a href="#">Contact Support</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'Footer',
};
</script>

<style scoped>
.footer-custom {
  background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
  border-top: 1px solid #e2e8f0;
  padding: 20px 0;
  margin-top: auto;
  font-size: 0.8125rem;
}

.text-muted {
  color: #adb5bd;
  font-weight: 400;
}

.text-secondary {
  color: #6c757d;
  font-weight: 400;
}

.footer-links {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  justify-content: flex-end;
  flex-wrap: wrap;
  gap: 20px;
}

.footer-links li {
  margin: 0;
}

.footer-links a {
  color: #6c757d;
  text-decoration: none;
  transition: color 0.2s ease;
  font-size: 0.8125rem;
  font-weight: 500;
}

.footer-links a:hover {
  color: #5a67d8;
}

@media (max-width: 768px) {
  .footer-custom {
    text-align: center;
  }

  .footer-links {
    justify-content: center;
    margin-top: 12px;
  }

  .col-md-6 {
    text-align: center !important;
  }
}
</style>
