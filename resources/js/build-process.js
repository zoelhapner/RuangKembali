window.initBuildProcess = function () {

    const weeks = window.BUILD_PROCESS_CONFIG.weekLabels;
        document
        .querySelectorAll('.just-col, .just-head, col.just-col')
        .forEach(el => el.classList.add('just-hidden'));
    applyAutoFreeze();

    setupJustekAccess();

    hitungFooter();
    filterWeek();
    hitungTotalPelaksanaan();
    
    calcTotal();

    updateKurvaChartRealtime();
    initTopScroll();
};

    window.initKurvaChart = function () {
        const ctx = document.getElementById('kurvaSChart');

        if (!ctx || typeof Chart === 'undefined') {
            return;
        }
        ctx.width = Math.max(BUILD_PROCESS_CONFIG.weekCount * 50, 900);
        const weekCount = window.BUILD_PROCESS_CONFIG.weekCount;
        const dataAwal = window.BUILD_PROCESS_CONFIG.kurvaData;

        const labels = [];

        for (let i = 1; i <= weekCount; i++) {
            labels.push('M' + i);
        }

        const realisasi = [];

        for (let i = 1; i <= weekCount; i++) {
            const found = dataAwal.find(d => d.week == i);
            realisasi.push(found ? found.progress : 0);
        }

        window.kurvaChart = new Chart(ctx, {

            type: 'line',

            data: {

                labels: labels,

                datasets: [

                    {
                        label:'Realisasi (%)',
                        data: realisasi,
                        tension:0.3
                    },

                    {
                        label:'Rencana (%)',
                        data:getPlanKumulatif(),
                        tension:0.3
                    }

                ]

            },

            options:{
                animation:false,
                responsive:true,
                maintainAspectRatio:false,

                scales:{
                    x:{
                        ticks:{
                            autoSkip:true,
                            maxTicksLimit:5
                        }
                    },

                    y:{
                        beginAtZero:true,
                        max:100
                    }
                }
            }

        });
    }

            function applyAutoFreeze() {
                const table = document.querySelector(".progress-table");
                if (!table) return;
                const colgroup = table?.querySelectorAll("colgroup col") || [];
                const freezeCount = 6;
                table.querySelectorAll(".sticky-col, .sticky-last").forEach(cell => {
                    cell.classList.remove("sticky-col", "sticky-last");
                    cell.style.left = "";
                    cell.style.width = "";
                });

                if (window.innerWidth < 576) {
                    return;
                }
                const offsets = [];
                let left = 0;
                for (let i = 0; i < freezeCount; i++) {
                    offsets.push(left);
                    left += Math.round(
                        parseFloat(getComputedStyle(colgroup[i]).width)
                    );
                }

                const rowspanMap = [];
                table.querySelectorAll("tr").forEach(row => {
                    let colIndex = 0;
                    Array.from(row.children).forEach(cell => {
                        while (rowspanMap[colIndex] && rowspanMap[colIndex] > 0) {
                            rowspanMap[colIndex]--;
                            colIndex++;
                        }

                        const colspan = parseInt(cell.getAttribute("colspan")) || 1;
                        const rowspan = parseInt(cell.getAttribute("rowspan")) || 1;

                        if (
                                colIndex < freezeCount ||
                                cell.classList.contains('freeze-col')
                            ) {
                            cell.classList.add("sticky-col");
                            if (colIndex === freezeCount - 1) {
                                cell.classList.add("sticky-last");
                            }
                            cell.style.left = Math.round(offsets[colIndex]) + "px";
                            // batasi width jika colspan > 1
                            let width = 0;
                            for (let i = 0; i < colspan && (colIndex + i) < freezeCount; i++) {
                                width += Math.round(parseFloat(
                                    getComputedStyle(colgroup[colIndex + i]).width)
                                );
                            }
                            cell.style.width = width + "px";
                        }

                        if (rowspan > 1) {
                            for (let i = 0; i < colspan; i++) {
                                rowspanMap[colIndex + i] = rowspan - 1;
                            }
                        }
                        colIndex += colspan;
                    });
                });

                // row-category: freeze td pertama
                table.querySelectorAll("tr.row-category, tr.row-uraian").forEach(row => {
                    const cell = row.querySelector("td");
                    if (!cell) return;
                    const width = Array.from(colgroup).slice(0, freezeCount).reduce((sum, c) => {
                        return sum + (parseFloat(getComputedStyle(c).width) || 0);
                    }, 0);
                    cell.classList.add("sticky-col");
                    cell.classList.add("sticky-last");

                    cell.style.left = "0px";
                    cell.style.width = Math.round(width) + "px";
                });
                // row tambahan (kuning)
                table.querySelectorAll("tr.row-tambahan-item").forEach(row => {

                    let left = 0;

                    Array.from(row.children).forEach((cell, index) => {

                        if (index < freezeCount) {

                            cell.classList.add("sticky-col");

                            if (index === freezeCount - 1) {
                                cell.classList.add("sticky-last");
                            }

                            cell.style.left = Math.round(left) + "px";

                            cell.style.zIndex = 55;

                            cell.style.background = "#fff3cd";

                            left += Math.round(parseFloat(
                                getComputedStyle(colgroup[index]).width)
                            );
                        }
                    });
                });
                const HEADER_ROW_HEIGHT = 60;

                const headerRows = table.querySelectorAll("thead tr");
                const firstRow = headerRows[0];
                const secondRow = headerRows[1];

                Array.from(firstRow.children).forEach(th => {
                    th.style.position = "sticky";
                    th.style.top = "0px";
                    th.style.zIndex = th.classList.contains("sticky-col") ? "155" : "102";
                    th.style.background = "#f8f9fa";
                });

                Array.from(secondRow.children).forEach(th => {
                    th.style.position = "sticky";
                    th.style.top = HEADER_ROW_HEIGHT + "px";
                    th.style.zIndex = th.classList.contains("sticky-col") ? "155" : "101";
                    th.style.background = "#f8f9fa";
                });
            }

            const weeks = window.BUILD_PROCESS_CONFIG.weekLabels;
            let activeWeek = null;

            const colsPerWeek = 6; 
            const colsPerubahan = 4; 
            
            function formatRupiah(angka) {
                angka = Number(angka || 0);
                return new Intl.NumberFormat('id-ID').format(angka);
            }

            function filterWeek(weekNo) {
                console.log("filterWeek()", weekNo);
                const table = document.querySelector(".progress-table");
                if (!table) return;

                const weekCells = [...table.querySelectorAll('[data-week]')];

                const colgroup = table.querySelectorAll("colgroup col");
                const weekCols = [...colgroup].filter(col => col.dataset.week);

                weekCols.forEach(col => {
                    col.classList.toggle(
                        'col-hidden',
                        weekNo && col.dataset.week != weekNo
                    );
                });

                weekCells.forEach(cell => {
                    cell.classList.toggle(
                        'col-hidden',
                        weekNo && cell.dataset.week != weekNo
                    );
                });
            }

            const weekSelect = $('#filter-week');

            weekSelect.off('change');   // hapus event lama
            weekSelect.on('change', function () {
                console.log('change', this.value);
                filterWeek(this.value);
            });
            // helper parse tanggal lokal (ANTI timezone shift)
            function parseLocalDate(dateStr) {

                if (!dateStr) return null;

                // format input date native: yyyy-mm-dd
                if (dateStr.includes("-")) {

                    const [y, m, d] = dateStr.split("-").map(Number);

                    return new Date(y, m - 1, d);
                }

                // format backend: dd/mm/yyyy
                if (dateStr.includes("/")) {

                    const [d, m, y] = dateStr.split("/").map(Number);

                    return new Date(y, m - 1, d);
                }

                return null;
            }

            const dateInput = document.getElementById('filter-date');

            if (dateInput) {

                dateInput.addEventListener('change', function() {

                    const selectedDate = this.value;

                    // reset kalau kosong
                    if (!selectedDate) {

                        $('#filter-week').val('').trigger('change');
                        filterWeek(null);

                        return;
                    }

                    const selected = parseLocalDate(selectedDate);

                    let foundWeek = null;

                    weeks.forEach(w => {

                        const start = parseLocalDate(w.start_date ?? w.start);
                        const end   = parseLocalDate(w.end_date ?? w.end);

                        if (!start || !end) return;

                        if (selected >= start && selected <= end) {
                            foundWeek = w.week_no;
                        }

                    });
                    console.log(weeks);

                    if (foundWeek) {

                        $('#filter-week')
                            .val(foundWeek)
                            .trigger('change');

                        // filterWeek(foundWeek);

                    } else {

                        $('#filter-week')
                            .val('')
                            .trigger('change');

                        // filterWeek(null);
                    }
                });
            }
        $('#btn-reset-filter').on('click', function () {

            // reset minggu
            $('#filter-week')
                .val('')
                .trigger('change');

            // reset tanggal
            $('#filter-date')
                .val('')
                .trigger('change');

        });
        function updateTotalDisplay(total) {

            document.querySelectorAll('.totalBobotKontrak')
                .forEach(el => {
                    el.innerText = total.toFixed(1);
                });
        }

        function calcTotal() {

            let total = 0;

            document.querySelectorAll('.bobot-input')
                .forEach(el => {

                    const val = parseFloat(el.value) || 0;

                    total += val;
                });

            total = Number(total.toFixed(3));

            updateTotalDisplay(total);

            return total;
        }
    
        document.addEventListener('click', function(e) {

            const btn = e.target.closest('.btn-just-toggle');
            if (!btn) return;

            const week = btn.dataset.week;

            const targets = document.querySelectorAll(
                `.just-col[data-week="${week}"],
                .just-head[data-week="${week}"]`
            );

            const cols = document.querySelectorAll(
                `col.just-col[data-week="${week}"]`
            );

            const isHidden = targets[0].classList.contains('just-hidden');

            targets.forEach(el => el.classList.toggle('just-hidden', !isHidden));
            cols.forEach(el => el.classList.toggle('just-hidden', !isHidden));

            btn.textContent = isHidden ? '−' : '+';
        });
    
            function recalcWeek(itemId, weekNo, itemVol, itemBobot)
            {
                itemVol = parseFloat(itemVol || 0);
                itemBobot = parseFloat(itemBobot || 0);

                const el = document.querySelector(
                    `.week-vol[data-item="${itemId}"][data-week="${weekNo}"]`
                );

                if (!el) return;
                const row = el.closest('tr');
                const vol = parseFloat(el.value || 0);

                const progress = itemVol > 0
                    ? (vol / itemVol) * 100
                    : 0;

                const bobot = progress * itemBobot / 100;
                row.querySelector(`.week-progress[data-week="${weekNo}"]`)
                    .innerText = progress.toFixed(2);

                row.querySelector(`.week-bobot[data-week="${weekNo}"]`)
                    .innerText = bobot.toFixed(3);
            }

            document.addEventListener('input', function(e) {

                if (!e.target.classList.contains('week-vol')) return;

                const tr = e.target.closest('tr');

                const item = e.target.dataset.item;
                const week = e.target.dataset.week;

                const itemVol = tr.dataset.itemVol || 0;
                const itemBobot = tr.dataset.itemBobot || 0;

                recalcWeek(item, week, itemVol, itemBobot);
                autosaveWeek(item, week);
                hitungFooter();
                updateKurvaChartRealtime();
            });

            document.querySelectorAll('.week-vol').forEach(el => {

                const tr = el.closest('tr');

                recalcWeek(
                    el.dataset.item,
                    el.dataset.week,
                    tr.dataset.itemVol || 0,
                    tr.dataset.itemBobot || 0
                );
            });

            hitungFooter();

            let autosaveTimer = {};

            function autosaveWeek(item, week) {

                const key = item + '-' + week;

                clearTimeout(autosaveTimer[key]);

                autosaveTimer[key] = setTimeout(() => {

                    const input = document.querySelector(
                        `.week-vol[data-item="${item}"][data-week="${week}"]`
                    );

                    const oldVal = input.dataset.last || 0;
                    const vol = parseFloat(input.value) || 0;

                    fetch(window.BUILD_PROCESS_CONFIG.routes.weeklyUpdate, {
                        method:"POST",
                        headers:{
                            "Content-Type":"application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify({
                            item_id: item,
                            week_no: week,
                            volume: vol,
                        })
                    })
                    .then(async r => {

                        const data = await r.json();

                        if (!r.ok) {
                            input.value = oldVal;
                            showToast(data.error || 'Volume melebihi kontrak');
                            throw new Error("422");
                        }
                        recalcWeek(item, week, 
                            input.closest('tr').dataset.itemVol,
                            input.closest('tr').dataset.itemBobot
                        );

                        hitungFooter();
                        updateKurvaChartRealtime();

                        return data;
                    })
                    .then(res => {

                        input.dataset.last = vol;

                        if (res.full) {
                            lockItemRow(item);
                        }
                        refreshInvoicePanel();
                    })
                    .catch(e => {
                        console.log("autosave rejected:", e.message);
                    });

                }, 400);
            }

            function hitungFooter() {

                let weekCount = window.WEEK_COUNT || 0;

                for (let w=1; w<=weekCount; w++) {

                    let sumBobot = 0;

                    document.querySelectorAll(`.week-bobot[data-week="${w}"]`)
                        .forEach(el => {
                            sumBobot += parseFloat(el.innerText || 0);
                        });

                    const bobotCell = document.getElementById(`sum-bobot-${w}`);
                    if (bobotCell) {
                        bobotCell.innerText = sumBobot.toFixed(2);
                    }
                }
            }
            function lockItemRow(itemId) {

                document.querySelectorAll(
                    `.week-vol[data-item="${itemId}"]`
                ).forEach(el => {
                    el.disabled = true;
                    el.classList.add('bg-light');
                });
            }
            function showToast(msg) {
                if (typeof toastr !== 'undefined') {
                    toastr.error(msg);
                } else {
                    alert(msg);
                }
            }
            hitungFooter();
            updateKurvaChartRealtime();

        function hitungTotalPelaksanaan() {

            let grandTotalVolume = 0;
            let grandTotalHarga = 0;
            let grandTotalJustek = 0;

            document.querySelectorAll('tr[data-item-id]').forEach(row => {

                const hargaCell = row.querySelector('.harga-kontrak');

                // skip row yang bukan item asli
                if (!hargaCell) return;

                const volKontrak = parseFloat(row.dataset.itemVol) || 0;

                const hargaKontrak =
                    parseFloat(hargaCell.dataset.price) || 0;

                let totalTambah = 0;
                let totalKurang = 0;
                let totalBaru = 0;

                row.querySelectorAll('.just-tambah').forEach(i => {
                    totalTambah += parseFloat(i.value) || 0;
                });

                row.querySelectorAll('.just-kurang').forEach(i => {
                    totalKurang += parseFloat(i.value) || 0;
                });

                row.querySelectorAll('.just-baru').forEach(i => {
                    totalBaru += parseFloat(i.value) || 0;
                });

                const totalJustek =
                    totalTambah - totalKurang + totalBaru;

                const volPelaksanaan =
                    volKontrak + totalJustek;

                let hargaPelaksanaan = 0;

                if (volKontrak > 0) {

                    hargaPelaksanaan =
                        (volPelaksanaan / volKontrak) *
                        hargaKontrak;

                } else {

                    hargaPelaksanaan =
                        totalJustek * hargaKontrak;
                }

                const colTotalJustek =
                    row.querySelector('.total-justek');

                const colVolPelaksanaan =
                    row.querySelector('.total-pelaksanaan');

                const colNilaiPelaksanaan =
                    row.querySelector('.nilai-pelaksanaan');

                if (colTotalJustek) {
                    colTotalJustek.textContent =
                        totalJustek.toFixed(3);
                }

                if (colVolPelaksanaan) {
                    colVolPelaksanaan.textContent =
                        volPelaksanaan.toFixed(3);
                }

                if (colNilaiPelaksanaan) {
                    colNilaiPelaksanaan.textContent =
                        'Rp ' +
                        Math.round(hargaPelaksanaan)
                            .toLocaleString('id-ID');
                }

                grandTotalJustek += totalJustek;
                grandTotalVolume += volPelaksanaan;
                grandTotalHarga += hargaPelaksanaan;
            });

            const grandVol =
                document.getElementById('grand-total-pelaksanaan');

            if (grandVol) {
                grandVol.textContent =
                    grandTotalVolume.toFixed(3);
            }

            const grandNilai =
                document.getElementById('grand-total-pelaksanaan-nilai');

            if (grandNilai) {
                grandNilai.textContent =
                    'Rp ' +
                    Math.round(grandTotalHarga)
                        .toLocaleString('id-ID');
            }

            const footerJustek =
                document.getElementById('grand-total-justek');

            if (footerJustek) {
                footerJustek.textContent =
                    grandTotalJustek.toFixed(3);
            }
        }
        function autosaveJustek(item, week) {

            const kurang = document.querySelector(
                `.just-kurang[data-item="${item}"][data-week="${week}"]`
            )?.value || 0;

            const tambah = document.querySelector(
                `.just-tambah[data-item="${item}"][data-week="${week}"]`
            )?.value || 0;

            const baru = document.querySelector(
                `.just-baru[data-item="${item}"][data-week="${week}"]`
            )?.value || 0;

            fetch(window.BUILD_PROCESS_CONFIG.routes.weeklyUpdate, {
                method: "POST",
                headers:{
                    "Content-Type":"application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({
                    item_id: item,
                    week_no: week,
                    just_kurang: kurang,
                    just_tambah: tambah,
                    just_baru: baru
                })
            })
            .then(r=>r.json())
            .then(res=>{

                fetch(window.BUILD_PROCESS_CONFIG.routes.invoiceJustek,{
                    method:"POST",
                    headers:{
                    "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content
                    }
                })
                .then(()=>{

                    refreshInvoicePanel();

                });

            })
            .catch(e=>{
            console.log("Justek autosave gagal");
            });
        }

        document.addEventListener('input', e => {

            if (
                e.target.classList.contains('just-kurang') ||
                e.target.classList.contains('just-tambah') ||
                e.target.classList.contains('just-baru')
            ) {

                const item = e.target.dataset.item;
                const week = e.target.dataset.week;

                hitungTotalPelaksanaan();
                autosaveJustek(item, week);
            }
        });

        const WEEK_LABELS = window.BUILD_PROCESS_CONFIG.weekLabels;

            document.addEventListener('click', async function(e) {

                const btn = e.target.closest('.btn-add-tambah');
                if (!btn) return;

                const itemId = btn.dataset.item;

                // cek editor
                let editorRow = document.querySelector(
                    `.row-editor[data-parent="${itemId}"]`
                );

                // kalau editor sudah ada → toggle editor saja
                if (editorRow) {

                    editorRow.classList.toggle('d-none');

                    return;
                }

                try {

                    btn.disabled = true;
                    btn.innerHTML = '...';

                    const response = await fetch(
                        `/projects/items/${itemId}/tambahan`
                    );

                    const html = await response.text();

                    const parentRow = document.querySelector(
                        `tr[data-item-id="${itemId}"]`
                    );

                    // cari posisi setelah row tambahan terakhir
                    let insertAfter = parentRow;

                    while (
                        insertAfter.nextElementSibling &&
                        insertAfter.nextElementSibling.classList.contains('row-tambahan-item')
                    ) {
                        insertAfter = insertAfter.nextElementSibling;
                    }

                    insertAfter.insertAdjacentHTML(
                        'afterend',
                        html
                    );

                    editorRow = insertAfter.nextElementSibling;

                    $(editorRow).find('.select2').select2({
                        width: '100%',
                    });

                    setupJustekAccess();

                } catch(err) {

                    console.error(err);

                    alert('Gagal load tambahan');

                } finally {

                    btn.disabled = false;
                    btn.innerHTML = '+';
                }
            });

            function buildWeekColumns(itemId) {
                let cols = '';

                WEEK_LABELS.forEach(w => {
                    cols +=
                    `
                        <td>
                            <input type="number"
                                step="0.01"
                                class="form-control week-vol"
                                data-item="${itemId}"
                                data-week="${w.week_no}"
                                value="">
                        </td>

                        <td class="week-progress"
                            data-week="${w.week_no}"
                            id="prog-${itemId}-${w.week_no}">
                        </td>

                        <td class="week-bobot"
                            data-week="${w.week_no}"
                            id="bobot-${itemId}-${w.week_no}">
                        </td>

                        <td class="just-col" data-week="${w.week_no}">
                            <input class="form-control just-kurang"
                                data-item="${itemId}"
                                data-week="${w.week_no}"
                                value="0">
                        </td>

                        <td class="just-col" data-week="${w.week_no}">
                            <input class="form-control just-tambah"
                                data-item="${itemId}"
                                data-week="${w.week_no}"
                                value="0">
                        </td>

                        <td class="just-col" data-week="${w.week_no}">
                            <input class="form-control just-baru"
                                data-item="${itemId}"
                                data-week="${w.week_no}"
                                value="0">
                        </td>
                    `;
                });

                return cols;
            }
            $(document).on('click', '.btn-simpan-tambahan', function (e) {
                e.preventDefault();
                e.stopPropagation();

                const parentId = this.dataset.item;

                const select = document.querySelector(
                    `.job-tambahan[data-item="${parentId}"]`
                );

                const jobId = select.value;
                if (!jobId) {
                    alert('Pilih pekerjaan tambahan dulu');
                    return;
                }

                fetch(window.BUILD_PROCESS_CONFIG.routes.storeTambahan, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({
                        project_id: window.BUILD_PROCESS_CONFIG.projectId,
                        parent_item_id: parentId,
                        job_category_id: jobId
                    })
                })
                .then(r => r.json())
                .then(res => {
                    if (!res.success) return;

                    const data = res.data;
                    const parentId = data.parent_id;

                    // aktifkan Pek.Baru parent
                    document.querySelectorAll(
                        `.just-baru[data-item="${parentId}"]`
                    ).forEach(input => input.readOnly = false);

                    const parentRow = document.querySelector(
                        `tr[data-item-id="${parentId}"]`
                    );

                    if (!parentRow) {
                        console.error('Parent row tidak ditemukan');
                        return;
                    }

                    const rupiah = new Intl.NumberFormat('id-ID');

                    const newRow = document.createElement('tr');
                    newRow.className = 'table-warning row-tambahan-item';
                    newRow.dataset.parent = parentId;
                    newRow.dataset.itemId = data.id;
                    newRow.dataset.itemVol = data.volume;
                    newRow.dataset.itemBobot = data.bobot_percent ?? 0;
                    const weekCols = buildWeekColumns(data.id);

                    newRow.innerHTML = `
                        <td></td>
                        <td>
                            ↳ ${data.uraian}
                            <span class="badge bg-warning text-dark">Tambahan</span>
                        </td>
                        <td>${data.satuan}</td>
                        <td>${data.volume}</td>
                        <td class="harga-kontrak" data-price="${data.price}">
                            Rp ${rupiah.format(data.price)}
                        </td>
                        <td></td>
                        ${weekCols}
                        <td class="total-justek" data-item="${data.id}">0</td>
                        <td class="total-pelaksanaan"
                            data-item="${data.id}"
                            data-vol-kontrak="${data.volume}">
                            ${data.volume}
                        </td>
                        <td class="harga-kontrak" data-price="${data.price}">
                            Rp ${rupiah.format(data.price)}
                        </td>
                        <td class="nilai-pelaksanaan">0</td>
                    `;

                    let insertAfter = parentRow;

                    // cari row tambahan terakhir
                    while (
                        insertAfter.nextElementSibling &&
                        (
                            insertAfter.nextElementSibling.classList.contains('row-tambahan-item') ||
                            insertAfter.nextElementSibling.classList.contains('row-editor')
                        )
                    ) {
                        insertAfter = insertAfter.nextElementSibling;
                    }

                    // insert langsung ke table utama
                    insertAfter.after(newRow);
                    requestAnimationFrame(() => {
                        applyAutoFreeze();
                    });
                    hitungTotalPelaksanaan();
                    setupJustekAccess();

                    $(select).val(null).trigger('change');
                });

                return false;
            });
            function setupJustekAccess() {
                document.querySelectorAll('tr[data-item-id]').forEach(row => {

                    const isTambahan = row.classList.contains('table-warning');

                    const inputKurang = row.querySelectorAll('.just-kurang');
                    const inputTambah = row.querySelectorAll('.just-tambah');
                    const inputBaru   = row.querySelectorAll('.just-baru');
                    const weekInputs  = row.querySelectorAll('.week-vol');
                    if (isTambahan) {
                        inputKurang.forEach(i => {
                            i.value = 0;
                            i.disabled = true;
                            i.classList.add('bg-light');
                        });
                        inputTambah.forEach(i => {
                            i.value = 0;
                            i.disabled = true;
                            i.classList.add('bg-light');
                        });
                        weekInputs.forEach(i => {
                            i.value = 0;
                            i.disabled = true;
                            i.classList.add('bg-light');
                        });
                        inputBaru.forEach(i => {
                            i.disabled = false;
                            i.classList.remove('bg-light');
                        });

                    } else {
                        inputBaru.forEach(i => {
                            i.value = 0;
                            i.disabled = true;
                            i.classList.add('bg-light');
                        });

                        inputKurang.forEach(i => {
                            i.disabled = false;
                            i.classList.remove('bg-light');
                        });

                        inputTambah.forEach(i => {
                            i.disabled = false;
                            i.classList.remove('bg-light');
                        });
                        weekInputs.forEach(i => {
                            i.disabled = false;
                            i.classList.remove('bg-light');
                        });
                    }
                });
            }
            setupJustekAccess();
    
        function refreshInvoicePanel() {

            fetch(window.BUILD_PROCESS_CONFIG.routes.invoicePanel)
            .then(res=>res.text())
            .then(html=>{
                document.getElementById('invoice-panel').innerHTML = html;
            });

        }
        const exportBtn = document.getElementById('btn-export-pdf');
        if(exportBtn){
            exportBtn.addEventListener('click', function(e){

                e.preventDefault();

                const week = exportBtn.getElementById('filter-week').value;

                const date = exportBtn.getElementById('filter-date').value;

                let url = window.BUILD_PROCESS_CONFIG.routes.exportPdf;

                const params = new URLSearchParams();

                if(week){
                    params.append('week', week);
                }

                if(date){
                    params.append('date', date);
                }

                window.open(
                    url + '?' + params.toString(),
                    '_blank'
                );

            });
        }
        function initTopScroll() {

            const topScroll = document.querySelector('.table-scroll-tops');
            const bottomScroll = document.querySelector('.table-real');

            if (!topScroll || !bottomScroll) {
                return;
            }

            const topContent = topScroll.querySelector('div');

            if (!topContent) {
                return;
            }

            function syncWidth() {

                const w = bottomScroll.scrollWidth;

                if (w > 0) {
                    topContent.style.width = w + 'px';
                }
            }

            const observer = new ResizeObserver(() => {
                syncWidth();
            });

            observer.observe(bottomScroll);

            window.addEventListener('resize', syncWidth);

            topScroll.addEventListener('scroll', () => {
                bottomScroll.scrollLeft = topScroll.scrollLeft;
            });

            bottomScroll.addEventListener('scroll', () => {
                topScroll.scrollLeft = bottomScroll.scrollLeft;
            });

            syncWidth();
        }