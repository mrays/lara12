# 🚀 Service Upgrade Request System

Sistem untuk mengelola permintaan upgrade service dari client ke admin dengan approval workflow yang lengkap.

## 📋 Fitur Utama

### 🎯 **Client Side Features:**
- ✅ Modal upgrade request dari halaman service management
- ✅ Form upgrade dengan price comparison
- ✅ Pilihan alasan upgrade (resources, features, growth, performance, other)
- ✅ Additional notes untuk detail tambahan
- ✅ Preview price difference sebelum submit
- ✅ Status tracking upgrade request

### 🛡️ **Admin Side Features:**
- ✅ Dashboard upgrade requests dengan filter dan search
- ✅ Status cards (Total, Pending, Approved, Rejected, Processing)
- ✅ Bulk actions untuk multiple requests
- ✅ Detail view dengan client dan service information
- ✅ Approve/Reject/Processing workflow
- ✅ Admin notes untuk feedback ke client
- ✅ Notification badge di sidebar

## 🔄 Workflow Process

```
Client Request → Pending → Admin Review → Approved/Rejected/Processing
```

### 1. **Client Submits Request**
- Client klik "Upgrade Layanan" di `/client/services/{id}/manage`
- Pilih plan dari modal pricing
- Isi form upgrade request dengan alasan dan notes
- System create record dengan status "pending"

### 2. **Admin Receives Notification**
- Badge merah di sidebar admin menunjukkan pending requests
- Admin dapat lihat semua requests di `/admin/upgrade-requests`
- Filter berdasarkan status, search by client/service

### 3. **Admin Processing**
- **Approve**: Request disetujui, client dapat proceed
- **Reject**: Request ditolak dengan alasan dari admin
- **Processing**: Mark sebagai sedang diproses
- Admin dapat tambahkan notes yang visible ke client

## 📁 File Structure

### **Models & Database**
```
app/Models/ServiceUpgradeRequest.php
database_queries/create_service_upgrade_requests.sql
DATABASE_SETUP.md (panduan setup manual)
```

### **Controllers**
```
app/Http/Controllers/ServiceUpgradeController.php          # Client controller
app/Http/Controllers/Admin/ServiceUpgradeController.php    # Admin controller
```

### **Views**
```
resources/views/admin/upgrade-requests/
├── index.blade.php                    # Admin list view
└── show.blade.php                     # Admin detail view

resources/views/client/services/manage.blade.php  # Updated with upgrade modal
```

### **Routes**
```php
// Client routes
Route::post('/services/{service}/upgrade-request', 'ServiceUpgradeController@submitRequest');
Route::get('/upgrade-requests', 'ServiceUpgradeController@clientRequests');
Route::get('/upgrade-requests/{request}', 'ServiceUpgradeController@clientShow');
Route::post('/upgrade-requests/{upgradeRequest}/cancel', 'ServiceUpgradeController@cancel');

// Admin routes
Route::get('/admin/upgrade-requests', 'Admin\ServiceUpgradeController@index');
Route::get('/admin/upgrade-requests/{upgradeRequest}', 'Admin\ServiceUpgradeController@show');
Route::post('/admin/upgrade-requests/{upgradeRequest}/approve', 'Admin\ServiceUpgradeController@approve');
Route::post('/admin/upgrade-requests/{upgradeRequest}/reject', 'Admin\ServiceUpgradeController@reject');
Route::post('/admin/upgrade-requests/{upgradeRequest}/processing', 'Admin\ServiceUpgradeController@markAsProcessing');
Route::post('/admin/upgrade-requests/bulk-action', 'Admin\ServiceUpgradeController@bulkAction');
```

## 🗃️ Database Schema

### **service_upgrade_requests Table**
```sql
CREATE TABLE service_upgrade_requests (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    service_id BIGINT NOT NULL,
    client_id BIGINT NOT NULL,
    current_plan VARCHAR(255) NOT NULL,
    requested_plan VARCHAR(255) NOT NULL,
    current_price DECIMAL(10,2) NOT NULL,
    requested_price DECIMAL(10,2) NOT NULL,
    upgrade_reason ENUM('need_more_resources', 'additional_features', 'business_growth', 'performance_improvement', 'other'),
    additional_notes TEXT NULL,
    status ENUM('pending', 'approved', 'rejected', 'processing') DEFAULT 'pending',
    admin_notes TEXT NULL,
    processed_by BIGINT NULL,
    processed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_status_created (status, created_at),
    INDEX idx_client_service (client_id, service_id)
);
```

## 🎨 UI Components

### **Client Modal Upgrade Request**
```html
<!-- Modal dengan form lengkap -->
- Current Plan vs Requested Plan comparison
- Price difference calculator
- Upgrade reason dropdown
- Additional notes textarea
- Real-time price comparison
```

### **Admin Dashboard**
```html
<!-- Status Cards -->
- Total Requests
- Pending (dengan highlight)
- Approved
- Processing

<!-- Filter & Search -->
- Status filter dropdown
- Search by client name/email/service
- Clear filters button

<!-- Actions -->
- Bulk approve/reject/processing
- Individual quick actions
- Detail view modal
```

## 🔧 API Endpoints

### **Client Endpoints**

#### Submit Upgrade Request
```http
POST /client/services/{service}/upgrade-request
Content-Type: application/json

{
    "requested_plan": "Premium Plan",
    "requested_price": 500000,
    "billing_cycle": "monthly",
    "upgrade_reason": "business_growth",
    "additional_notes": "Need more storage and bandwidth"
}

Response:
{
    "success": true,
    "message": "Upgrade request submitted successfully!",
    "request_id": 123
}
```

### **Admin Endpoints**

#### Approve Request
```http
POST /admin/upgrade-requests/{id}/approve
Content-Type: application/json

{
    "admin_notes": "Request approved. Upgrade will be processed within 24 hours."
}

Response:
{
    "success": true,
    "message": "Upgrade request approved successfully!"
}
```

#### Reject Request
```http
POST /admin/upgrade-requests/{id}/reject
Content-Type: application/json

{
    "admin_notes": "Request rejected due to insufficient account history."
}

Response:
{
    "success": true,
    "message": "Upgrade request rejected successfully!"
}
```

#### Bulk Action
```http
POST /admin/upgrade-requests/bulk-action
Content-Type: application/json

{
    "action": "approve",
    "request_ids": [1, 2, 3],
    "admin_notes": "Bulk approval for qualified accounts"
}

Response:
{
    "success": true,
    "message": "3 request(s) approved successfully!"
}
```

## 🚀 Usage Examples

### **Client Usage**
```javascript
// Client clicks upgrade button
function upgradeService() {
    // Open pricing modal
    const modal = new bootstrap.Modal(document.getElementById('upgradePlanModal'));
    modal.show();
}

// Client selects plan
function selectPlan(packageId, packageName, price) {
    // Fill upgrade request form
    document.getElementById('requested_plan').value = packageName;
    document.getElementById('requested_price').value = price;
    
    // Show upgrade request modal
    const upgradeModal = new bootstrap.Modal(document.getElementById('upgradeRequestModal'));
    upgradeModal.show();
}

// Submit upgrade request
function submitUpgradeRequest() {
    const formData = new FormData(document.getElementById('upgradeRequestForm'));
    
    fetch('/client/services/1/upgrade-request', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Upgrade request submitted successfully!');
        }
    });
}
```

### **Admin Usage**
```javascript
// Quick approve action
function quickAction(action, requestId) {
    const notes = prompt('Add admin notes:');
    
    fetch(`/admin/upgrade-requests/${requestId}/${action}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            admin_notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

// Bulk action
function bulkAction(action) {
    const selectedIds = getSelectedRequestIds();
    const notes = prompt(`Add notes for bulk ${action}:`);
    
    fetch('/admin/upgrade-requests/bulk-action', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            action: action,
            request_ids: selectedIds,
            admin_notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
```

## 🔒 Security Features

### **Authorization**
- ✅ Client hanya bisa submit request untuk service milik sendiri
- ✅ Client hanya bisa lihat request milik sendiri
- ✅ Admin memerlukan role 'admin' untuk akses halaman admin
- ✅ CSRF protection pada semua form

### **Validation**
- ✅ Service ownership validation
- ✅ Duplicate request prevention (1 pending per service)
- ✅ Required fields validation
- ✅ Price and plan validation
- ✅ Admin notes required untuk reject action

### **Data Integrity**
- ✅ Foreign key constraints
- ✅ Enum validation untuk status dan reason
- ✅ Soft delete protection dengan cascade rules

## 📊 Monitoring & Analytics

### **Admin Dashboard Metrics**
```php
// Status counts untuk dashboard
$statusCounts = [
    'all' => ServiceUpgradeRequest::count(),
    'pending' => ServiceUpgradeRequest::where('status', 'pending')->count(),
    'approved' => ServiceUpgradeRequest::where('status', 'approved')->count(),
    'rejected' => ServiceUpgradeRequest::where('status', 'rejected')->count(),
    'processing' => ServiceUpgradeRequest::where('status', 'processing')->count(),
];
```

### **Notification System**
```php
// Badge count di sidebar
$pendingCount = ServiceUpgradeRequest::where('status', 'pending')->count();
```

## 🔄 Future Enhancements

### **Planned Features**
- [ ] Email notifications untuk client dan admin
- [ ] Real-time notifications dengan WebSocket
- [ ] Auto-approve berdasarkan criteria tertentu
- [ ] Integration dengan payment system
- [ ] Upgrade history tracking
- [ ] Client upgrade request limits
- [ ] Advanced reporting dan analytics

### **Integration Points**
- [ ] **Invoice System**: Auto-generate invoice setelah approve
- [ ] **Email System**: Send notifications via Gmail SMTP
- [ ] **Payment Gateway**: Process upgrade payments
- [ ] **Service Management**: Auto-update service setelah payment

## 🐛 Troubleshooting

### **Common Issues**

#### Request tidak ter-submit
```bash
# Check CSRF token
# Check route permissions
# Check validation errors
```

#### Badge count tidak update
```bash
# Clear cache
php artisan cache:clear
php artisan view:clear
```

#### Admin tidak bisa approve
```bash
# Check user role
# Check route middleware
# Check database permissions
```

## 📞 Support

Untuk bantuan teknis atau pertanyaan tentang sistem upgrade request, hubungi tim development atau buat issue di repository project.
