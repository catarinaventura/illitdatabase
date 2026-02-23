let catalogData = null;

/* --------------------------------------------------------------------
SEGURANÇA PHP
-------------------------------------------------------------------- */
const safeIsLoggedIn =
    typeof isLoggedIn !== "undefined" ? isLoggedIn : false;

const safeUserRole =
    typeof userRole !== "undefined" ? userRole : "guest";

/* --------------------------------------------------------------------
NOTIFICAÇÕES TOAST
-------------------------------------------------------------------- */
function showToast(message, type = 'primary') {
    const toastEl = document.getElementById('cart-toast');
    const toastMessage = document.getElementById('cart-toast-message');

    if (!toastEl || !toastMessage) return;

    // Mensagem
    toastMessage.textContent = message;

    // Mudar o tipo de toast
    toastEl.className = `toast align-items-center text-bg-${type} border-0`;

    // Iniciar o toast
    const bsToast = new bootstrap.Toast(toastEl);
    bsToast.show();
}

/* --------------------------------------------------------------------
ADICIONAR AO CARRINHO
-------------------------------------------------------------------- */
function addToCart(productId) {
    fetch("cart_action.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            "X-Requested-With": "XMLHttpRequest"
        },
        body: `action=add&product_id=${productId}&quantity=1`
    })
    .then(res => res.json())
    .then(data => {
        if (data.notification) {
            showToast(data.notification, data.type);
        }
    })
    .catch(() => showToast("Erro ao adicionar ao carrinho!", "danger"));
}

/* --------------------------------------------------------------------
CARREGAR PRODUTOS
-------------------------------------------------------------------- */
fetch("get_products.php")
    .then(res => res.json())
    .then(data => {
        catalogData = data;
        loadProducts(data.products);
        loadProductSelect(data.products);
        loadCategories(data.categories);
    })
    .catch(err => console.error("Erro ao carregar produtos:", err));

/* --------------------------------------------------------------------
CATEGORIAS
-------------------------------------------------------------------- */
function loadCategories(categories) {
    const select = document.getElementById("category-filter");
    if (!select) return;

    select.innerHTML = `<option value="">Todas as Categorias</option>`;

    categories.forEach(cat => {
        const option = document.createElement("option");
        option.value = cat.id;
        option.textContent = cat.name;
        select.appendChild(option);
    });

    select.addEventListener("change", () => {
        const selected = select.value;
        const filtered = selected
            ? catalogData.products.filter(p => p.category === selected)
            : catalogData.products;

        loadProducts(filtered);
    });
}

/* --------------------------------------------------------------------
PRODUTOS
-------------------------------------------------------------------- */
function loadProducts(products) {
    const container = document.getElementById("product-list");
    if (!container) return;

    container.innerHTML = "";

    products.forEach(product => {
        const div = document.createElement("div");
        div.classList.add("product");

        const showCartButton = safeUserRole !== "admin" && product.stock > 0;;

        // Se stock = 0
        const soldOutClass = product.stock <= 0 ? "sold-out" : "";


        div.innerHTML = `
            <h3>${product.name}</h3>
            <p style="opacity:60%">${product.type}</p>
            <img src="${product.images[0]}" class="product-card-img">
            <p>${product.price} €</p>
            <button class="details-btn">Detalhes</button>
            ${safeUserRole !== "admin" &&product.stock <= 0 ? `<button class="btn btn-secondary">Sem Stock</button>` : ""}
            ${showCartButton ? `<button class="adicionar-btn">Adicionar ao Carrinho</button>` : ""}
        `;

        // Lightbox
        div.querySelector(".details-btn")
            .addEventListener("click", () => openLightbox(product));

        // Carrinho
        if (showCartButton) {
            div.querySelector(".adicionar-btn")
                .addEventListener("click", () => {
                    if (!safeIsLoggedIn) {
                        alert("Tens de iniciar sessão para adicionar produtos!");
                        return;
                    }
                    addToCart(product.id);
                });
        }

        container.appendChild(div);
    });
}

/* --------------------------------------------------------------------
SELECIONAR PRODUTOS
-------------------------------------------------------------------- */
function loadProductSelect(products) {
    const select = document.getElementById("product");
    if (!select) return;

    select.innerHTML = "";

    const placeholder = document.createElement("option");
    placeholder.disabled = true;
    placeholder.selected = true;
    placeholder.textContent = "-";
    select.appendChild(placeholder);

    products.forEach(p => {
        const option = document.createElement("option");
        option.value = p.id;
        option.textContent = p.name;
        select.appendChild(option);
    });
}

/* --------------------------------------------------------------------
CALCULADORA
-------------------------------------------------------------------- */
const toggleBtn = document.getElementById("open-calc");
const orderForm = document.getElementById("order-form");

// OVERLAY
let overlay = document.createElement("div");
overlay.id = "calc-overlay";
overlay.style.position = "fixed";
overlay.style.inset = "0";
overlay.style.backgroundColor = "rgba(0,0,0,0.15)";
overlay.style.opacity = "0";
overlay.style.pointerEvents = "none";
overlay.style.transition = "opacity 0.3s ease";
overlay.style.zIndex = "999";
document.body.appendChild(overlay);

toggleBtn.addEventListener("click", () => {
    const isActive = orderForm.classList.toggle("active");

    if (isActive) {
        overlay.style.opacity = "1";
        overlay.style.pointerEvents = "all";
    } else {
        overlay.style.opacity = "0";
        overlay.style.pointerEvents = "none";
    }
});

document.addEventListener("click", (e) => {
    if (!orderForm.contains(e.target) && e.target !== toggleBtn) {
        orderForm.classList.remove("active");
        overlay.style.opacity = "0";
        overlay.style.pointerEvents = "none";
    }
});

document.getElementById("calculate").addEventListener("click", () => {
    const productId = document.getElementById("product").value;
    const quantity = parseFloat(document.getElementById("quantity").value);

    if (!productId) return alert("Escolha um produto!");
    if (quantity < 1) return alert("Quantidade inválida!");
    if (quantity - Math.floor(quantity) !== 0) return alert("Quantidade inválida!");

    const product = catalogData.products.find(p => p.id === productId);
    const total = parseFloat(product.price) * quantity;
    document.getElementById("total-value").textContent = `${total.toFixed(2)} €`;
});

/* --------------------------------------------------------------------
LIGHTBOX
-------------------------------------------------------------------- */
function openLightbox(product) {
    const lightbox = document.getElementById("lightbox");
    const title = document.getElementById("lightbox-title");
    const image = document.getElementById("lightbox-image");
    const price = document.getElementById("lightbox-price");
    const desc = document.getElementById("lightbox-description");

    if (!lightbox) return;

    title.textContent = product.name;
    image.src = product.images[0];
    price.textContent = `${product.price} €`;
    desc.textContent = product.description ?? "";

    lightbox.classList.add("show");
}

// Fechar lightbox ao clicar fora
const lightbox = document.getElementById("lightbox");
if (lightbox) {
    lightbox.addEventListener("click", e => {
        if (e.target === lightbox) {
            lightbox.classList.remove("show");
        }
    });
}

