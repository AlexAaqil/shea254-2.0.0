document.addEventListener('DOMContentLoaded', function () {
    var burgerIcon = document.getElementById('burgerIcon');
    var navLinks = document.querySelector('.nav_links');

    // Check if burgerIcon exists before adding event listener
    if (burgerIcon) {
        burgerIcon.addEventListener('click', function () {
            navLinks.classList.toggle('show');
            burgerIcon.classList.toggle('active_burger', navLinks.classList.contains('show'));
        });
    }
});

function searchFunction() {
    // Get the input value
    var input = document.getElementById("myInput");
    var filter = input.value.toUpperCase();

    // Get all elements with the class name "item"
    var items = document.getElementsByClassName("searchable");

    // Loop through all items and hide those that don't match the search query
    for (var i = 0; i < items.length; i++) {
        var item = items[i];
        var text = item.textContent || item.innerText;

        if (text.toUpperCase().indexOf(filter) > -1) {
            item.style.display = "";
        } else {
            item.style.display = "none";
        }
    }
}

function showConfirmationDialog(message, onConfirm) {
    Swal.fire({
        title: "Are you sure?",
        text: message,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete!",
    }).then((result) => {
        if (result.isConfirmed) {
            onConfirm();
        }
    });
}

// For forms: deleteItem(itemId, itemName);
// For links: deleteItem(itemId, itemName, url);

function deleteItem(itemId, itemName, url = null) {
    const message = `This ${itemName} will be deleted permanently!`;

    // Show a confirmation dialog
    showConfirmationDialog(message, () => {
        const formId = `deleteForm_${itemId}`;
        const form = document.getElementById(formId);

        if (form) {
            form.submit();
        }
    });
}