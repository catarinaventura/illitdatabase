document.addEventListener("DOMContentLoaded", () => {

/* --------------------------------------------------------------------
EDITAR USERS
-------------------------------------------------------------------- */
    document.querySelectorAll('.edit-user-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const row = btn.closest('tr');
            const username = row.querySelector('.username').textContent.trim();
            const email = row.querySelector('.email').textContent.trim();

            row.querySelector('.username').innerHTML = `<input type="text" name="username[${id}]" value="${username}" class="form-control">`;
            row.querySelector('.email').innerHTML = `<input type="email" name="email[${id}]" value="${email}" class="form-control">`;

            btn.outerHTML = `<button class="btn btn-success btn-sm save-user-btn" data-id="${id}">Guardar</button>`;

            row.querySelector('.save-user-btn').addEventListener('click', () => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="edit_id" value="${id}">
                    <input type="hidden" name="username[${id}]" value="${row.querySelector('input[name^=username]').value}">
                    <input type="hidden" name="email[${id}]" value="${row.querySelector('input[name^=email]').value}">
                `;
                document.body.appendChild(form);
                form.submit();
            });
        });
    });

/* --------------------------------------------------------------------
EDITAR PRODUTOS
-------------------------------------------------------------------- */
    document.querySelectorAll('.edit-product-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        const row = btn.closest('tr');

        const name = row.cells[2].textContent.trim();
        const category = row.cells[3].textContent.trim();
        const description = row.cells[4].textContent.trim();
        const price = row.cells[5].textContent.replace('€','').trim();
        const stock = row.cells[6].textContent.trim();

        row.cells[2].innerHTML = `<input type="text" name="name[${id}]" value="${name}" class="form-control">`;
        row.cells[3].innerHTML = `
            <select name="category[${id}]" class="form-control">
                <option value="albuns" ${category.toLowerCase() === 'albuns' ? 'selected' : ''}>Álbuns</option>
                <option value="merch" ${category.toLowerCase() === 'merch' ? 'selected' : ''}>Merchandise</option>
            </select>
        `;
        row.cells[4].innerHTML = `<textarea name="description[${id}]" class="form-control">${description}</textarea>`;
        row.cells[5].innerHTML = `<input type="number" step="0.01" name="price[${id}]" value="${price}" class="form-control">`;
        row.cells[6].innerHTML = `<input type="number" name="stock[${id}]" value="${stock}" class="form-control">`;

        btn.outerHTML = `<button class="btn btn-success btn-sm save-product-btn" data-id="${id}">Guardar</button>`;

        row.querySelector('.save-product-btn').addEventListener('click', () => {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="product_action" value="edit_product">
                <input type="hidden" name="edit_product_id" value="${id}">
                <input type="hidden" name="name[${id}]" value="${row.querySelector('input[name^=name]').value}">
                <input type="hidden" name="category[${id}]" value="${row.querySelector('select[name^=category]').value}">
                <input type="hidden" name="description[${id}]" value="${row.querySelector('textarea[name^=description]').value}">
                <input type="hidden" name="price[${id}]" value="${row.querySelector('input[name^=price]').value}">
                <input type="hidden" name="stock[${id}]" value="${row.querySelector('input[name^=stock]').value}">
            `;
            document.body.appendChild(form);
            form.submit();
        });
    });
});

});