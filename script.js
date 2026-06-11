function fetchInventory() {
    fetch("process.php?action=get_inventory")
    .then(res => res.json())
    .then(products => {
        const tableBody = document.getElementById("inventoryTable");
        const selectDropdown = document.getElementById("saleProductSelect");
        
        tableBody.innerHTML = "";
        selectDropdown.innerHTML = '<option value="">-- Choose Product --</option>';

        products.forEach(p => {
            tableBody.innerHTML += `
                <tr>
                    <td class="fw-bold">${p.name}</td>
                    <td>ksh ${parseFloat(p.price).toFixed(2)}</td>
                    <td><span class="badge ${p.qty > 5 ? 'bg-success':'bg-danger'}">${p.qty} items</span></td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button onclick="editProduct('${p.id}', '${escapeHtml(p.name)}', ${p.price}, ${p.qty})" class="btn btn-outline-warning">Edit</button>
                            <button onclick="deleteProduct('${p.id}')" class="btn btn-outline-danger">Remove</button>
                        </div>
                    </td>
                </tr>
            `;
            if(p.qty > 0) {
                selectDropdown.innerHTML += `<option value="${p.id}">${p.name} (ksh ${p.price})</option>`;
            }
        });
    });
}

// Helper function to load selected item values back into your product entry form
function editProduct(id, name, price, qty) {
    // Change form heading to signal update mode
    const formTitle = document.querySelector("#inventory card h4, #productForm h4") || { innerText: "" };
    
    document.getElementById("prodName").value = name;
    document.getElementById("prodPrice").value = price;
    document.getElementById("prodQty").value = qty;
    
    // Attach the ID dynamically to the form dataset so your submit action knows it's an update
    const form = document.getElementById("productForm");
    form.dataset.editId = id;
    
    // Change submit button appearance to highlight update state
    const submitBtn = form.querySelector("button[type='submit']");
    submitBtn.innerText = "Update Product Info";
    submitBtn.className = "btn btn-warning w-100 fw-bold";
}

// Updated form submit handler to balance regular creates vs updates
document.getElementById("productForm").addEventListener("submit", function(e) {
    e.preventDefault();
    
    const editId = this.dataset.editId;
    
    // Base action defaults to add_product unless an editId is pinned to the form state
    const data = {
        action: editId ? "update_product" : "add_product",
        name: document.getElementById("prodName").value,
        price: parseFloat(document.getElementById("prodPrice").value),
        qty: parseInt(document.getElementById("prodQty").value)
    };
    
    if (editId) {
        data.id = editId;
    }

    sendData(data, () => {
        this.reset();
        delete this.dataset.editId; // Clear structural edit flag
        
        // Reset submit button text back to original state
        const submitBtn = this.querySelector("button[type='submit']");
        submitBtn.innerText = "Add to Stock";
        submitBtn.className = "btn btn-success w-100 fw-bold";
        
        fetchInventory();
    });
});

function deleteProduct(id) {
    if(confirm("Are you sure you want to delete this item permanently?")) {
        sendData({ action: "delete_product", id: id }, () => {
            fetchInventory();
        });
    }
}

// Small utility function to prevent syntax breakage from quotes in item names
function escapeHtml(string) {
    return String(string).replace(/'/g, "\\'").replace(/"/g, "&quot;");
}



    const ctx = document.getElementById('weeklySalesChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar', 
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], // Days of the week
            datasets: [{
                label: 'Daily Revenue (Ksh)',
                data: [15000, 22000, 18500, 29000, 35000, 42000, 28000], // Replace with your actual sales data
                backgroundColor: 'rgba(25, 135, 84, 0.2)', // Match Bootstrap's "success" green (bg-success)
                borderColor: 'rgba(25, 135, 84, 1)',       // Solid green border
                borderWidth: 2,
                borderRadius: 5 // Gives the bars slightly rounded top corners
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false // Hides the legend since the chart title is self-explanatory
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Ksh ' + value.toLocaleString(); // Formats Y-axis values to Ksh
                        }
                    }
                }
            }
        }
    });
