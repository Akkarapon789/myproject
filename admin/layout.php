<!-- layout.php -->
<!-- ✅ Bootstrap & DataTables CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/2.0.3/css/dataTables.bootstrap5.min.css" rel="stylesheet">

<style>
/* 🎨 === Global Theme === */
body {
  font-family: "Prompt", sans-serif;
  background-color: #f5f6fa;
}

/* 🧭 Sidebar */
.sidebar {
  min-height: 100vh;
  background: #1e293b; /* สีกรม */
  color: #fff;
}
.sidebar h4 {
  padding: 15px;
  font-weight: bold;
  border-bottom: 1px solid #475569;
}
.sidebar a {
  color: #fff;
  text-decoration: none;
  display: block;
  padding: 12px 20px;
  border-radius: 6px;
  transition: 0.3s;
}
.sidebar a:hover,
.sidebar a.active {
  background: #334155;
}

/* 📦 Content Zone */
.content {
  flex-grow: 1;
  padding: 30px;
  background: #fff;
  border-radius: 12px;
  margin: 20px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}
.card {
  border-radius: 12px;
  box-shadow: 0 3px 6px rgba(0, 0, 0, 0.08);
}
.btn {
  border-radius: 6px;
}

/* 📊 === DataTables Styling === */
.dataTables_wrapper .dataTables_paginate .paginate_button {
  padding: 6px 12px;
  border-radius: 8px;
  background: transparent;
  border: 1px solid #dee2e6;
  margin: 0 2px;
  font-weight: 500;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
  background-color: #2155CD !important;
  color: #fff !important;
  border-color: #2155CD !important;
}
.table thead th {
  background-color: #2155CD !important;
  color: white;
  text-align: center;
  vertical-align: middle;
}
.table tbody td {
  vertical-align: middle;
  text-align: center;
}
.table img {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid #ddd;
}

/* Responsive */
@media (max-width: 991px) {
  .sidebar {
    min-height: auto;
  }
  .content {
    margin: 10px;
    padding: 20px;
  }
}
</style>

<!-- ✅ Bootstrap & DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.min.js"></script>
