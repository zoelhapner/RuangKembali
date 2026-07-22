<div class="modal fade" id="modalSearchProduct" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Cari Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <input type="text" id="searchProductInput" class="form-control mb-3"
                       placeholder="Cari nama produk...">

                <div id="searchResults" class="mt-2"></div>

            </div>

        </div>
    </div>
</div>

<script>
document.getElementById('searchProductInput').addEventListener('keyup', function () {
    let q = this.value;

    if (q.length < 2) {
        document.getElementById('searchResults').innerHTML = '';
        return;
    }

    fetch("{{ route('supplier.products.search', $supplier->id) }}?q=" + q)
        .then(res => res.json())
        .then(data => {
            document.getElementById('searchResults').innerHTML = data.html;
        });
});
</script>
