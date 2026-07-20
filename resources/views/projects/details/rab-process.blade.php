@php
$rab = $project->rab()->with([
    'categories.uraians.items',
    'categories.uraians.images.image'
])->first();
function numberToLetters($num) {
    $letters = '';
    $num = $num + 1;

    while ($num > 0) {
        $rem = ($num - 1) % 26;
        $letters = chr(65 + $rem) . $letters;
        $num = intdiv(($num - 1), 26);
    }

    return $letters;
}
@endphp

@can('lihat data proyek')
@if($rab)
<div class="card shadow-sm border-0 mb-4">

    <div class="card-body">

        <div class="row g-4">
            <div class="col-md-4">
                <label class="fw-semibold">Nama Customer</label>
                <input type="text" class="form-control" readonly
                       value="{{ $rab->contact_name }}">
            </div>
            <div class="col-md-4">
                <label class="fw-semibold">Lokasi Pekerjaan</label>
                <input type="text" class="form-control" readonly
                       value="{{ $rab->job_location }}">
            </div>
            <div class="col-md-4">
                <label class="fw-semibold">Durasi Pekerjaan</label>
                <input type="text" class="form-control" readonly
                       value="{{ $rab->job_duration }}">
            </div>
        </div>

        <h5 class="fw-bold mt-5 mb-3">Rincian Pekerjaan</h5>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th width="50">NO</th>
                        <th>URAIAN PEKERJAAN</th>
                        <th>SAT</th>
                        <th>VOL</th>
                        <th>HARGA SATUAN</th>
                        <th>JUMLAH HARGA</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($rab->categories as $category)

                        @php
                            $categoryLetter = numberToLetters($loop->index);
                            $uraianNo = 1;

                            $categoryTotal = $category->uraians
                                ->flatMap->items
                                ->sum('total');
                        @endphp

                            <tr class="table-secondary">
                                <th>{{ $categoryLetter }}</th>
                                <th colspan="4">{{ strtoupper($category->name) }}</th>
                                <th class="text-end">
                                    Rp {{ number_format($categoryTotal,2,',','.') }}
                                </th>
                            </tr>

                        @foreach($category->uraians as $uraian)

                            <tr class="fw-bold">
                                <td>{{ $uraianNo }}</td>

                                <td colspan="5">
                                    <div class="d-flex align-items-center gap-2">

                                        {{ ucwords($uraian->name) }}

                                        <button type="button"
                                            class="btn btn-sm btn-gambar"
                                            onclick="bukagaleri(
                                                '{{ route('rab.uraian-images', $uraian->id) }}',
                                                '{{ $uraian->name }}'
                                            )">

                                            <i class="ti ti-photo"></i>

                                        </button>

                                    </div>
                                </td>

                            </tr>

                            @php $itemNo = 1; @endphp

                            @foreach($uraian->items as $item)

                            <tr>

                                <td>{{ $uraianNo.'.'.$itemNo }}</td>

                                <td>{{ $item->job_name }}</td>

                                <td>{{ $item->satuan }}</td>

                                <td>{{ rtrim(rtrim(number_format($item->volume, 2, '.', ''), '0'), '.') }}</td>

                                <td>
                                    Rp {{ number_format($item->price,2,',','.') }}
                                </td>

                                <td class="text-end">
                                    Rp {{ number_format($item->total,2,',','.') }}
                                </td>

                            </tr>

                            @php $itemNo++; @endphp

                            @endforeach

                            @php $uraianNo++; @endphp

                        @endforeach

                    @endforeach

                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="5" class="text-end">SUBTOTAL</th>
                        <th>Rp {{ number_format($rab->subtotal,3,',','.') }}</th>
                    </tr>
    
                    <tr>
                        <th colspan="5" class="text-end">DISCOUNT</th>
                        <th>Rp {{ number_format($rab->discount,3,',','.') }}</th>
                        
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end">SUBTOTAL AFTER DISCOUNT</th>
                        <th>Rp {{ number_format($rab->subtotal_after_discount,3,',','.') }}</th>
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end">TAX RATE</th>
                        <th>{{ $rab->tax_rate }}%</th>
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end">TOTAL TAX</th>
                        <th>Rp {{ number_format($rab->tax_total,2,',','.') }}</th>
                        
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end">SHIPPING / HANDLING</th>
                        <th>Rp {{ number_format($rab->shipping,2,',','.') }}</th>
                        
                    </tr>

                    <tr>
                        <th colspan="5" class="text-end fw-bold">GRAND TOTAL</th>
                        <th class="fw-bold">
                            Rp {{ number_format($rab->grand_total,3,',','.') }}
                        </th>
                        
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($rab->notes)
            <div class="mt-4">
                <h5 class="fw-bold">Keterangan</h5>
                <div class="border p-3">{{ $rab->notes }}</div>
            </div>
        @endif
        <div class="d-flex align-items-center gap-2 mt-4">
            @if($project->rab?->id)
                
            <a href="{{ route('projects.rab.pdf', $project->id) }}"
                class="btn btn-dark"
                target="_blank"
                title="Download PDF">
                    <i class="ti ti-download"></i>Download PDF
            </a>
                
            @endif
        </div>
        @if(!$ReadOnly)
            <div class="card mt-3">
                <div class="card-body text-muted small">
                    <div>Dibuat oleh: {{ $rab->creator?->fullname ?? '-' }}</div>
                    <div>Dibuat pada: {{ $rab->created_at?->format('d M Y H:i') }}</div>
                    <div>Terakhir diubah: {{ $rab->updated_at?->format('d M Y H:i') }}</div>
                    <div>Diubah oleh: {{ $rab->editor?->fullname ?? '-' }}</div>
                </div>
            </div>
        @endif
    </div>
</div>
    <div class="modal fade" id="uraianGalleryModall">
        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 id="uraianGalleryTitle"></h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div id="uraianGalleryContainer"
                        class="d-flex flex-wrap gap-2">
                    </div>

                </div>

            </div>
        </div>
    </div>
    <div id="imageViewer" class="image-viewer d-none">

        <div class="viewer-toolbar">
            <button onclick="zoomOut()">-</button>
            <button onclick="zoomIn()">+</button>
            <button onclick="closeViewer()">✕</button>
        </div>

        <button class="viewer-prev" onclick="prevImage()">‹</button>

        <div class="viewer-stage">
            <img id="viewerImage">
        </div>

        <button class="viewer-next" onclick="nextImage()">›</button>

    </div>
@endif
@endcan
@push('js')
<script>

let viewerImages = []
let currentIndex = 0
let scale = 1

function bukagaleri(url, uraianName){

    const modal = new bootstrap.Modal(
        document.getElementById('uraianGalleryModall')
    )

    const title = document.getElementById('uraianGalleryTitle')
    const container = document.getElementById('uraianGalleryContainer')

    title.innerText = uraianName
    container.innerHTML = '<div class="text-muted">Loading...</div>'

    fetch(url)

    .then(async res => {

        if(!res.ok){

            const text = await res.text()

            console.error(text)

            throw new Error('Gagal load gambar')
        }

        return res.json()
    })

    .then(data => {

        container.innerHTML = ''

        if(data.length === 0){

            container.innerHTML =
                '<div class="text-muted">Belum ada gambar</div>'

            return
        }

        viewerImages = data.map(i => i.url)

        data.forEach((img,index)=>{

            container.insertAdjacentHTML('beforeend',`

                <img src="${img.url}"
                    class="rab-gallery-img"
                    data-index="${index}">

            `)

        })

    })

    .catch(err => {

        console.error(err)

        container.innerHTML = `
            <div class="text-danger">
                Gagal memuat gambar
            </div>
        `
    })

    modal.show()
}

document.addEventListener("click",function(e){

    if(e.target.classList.contains('rab-gallery-img')){

        const index = e.target.dataset.index
        openViewer(viewerImages,index)

    }

})

function openViewer(images,index=0){

    viewerImages = images
    currentIndex = parseInt(index)
    scale = 1

    document.getElementById("viewerImage").src = images[currentIndex]

    document
    .getElementById("imageViewer")
    .classList.remove("d-none")

}

function closeViewer(){

    document
    .getElementById("imageViewer")
    .classList.add("d-none")

}

function nextImage(){

    currentIndex++

    if(currentIndex >= viewerImages.length){
        currentIndex = 0
    }

    document.getElementById("viewerImage").src = viewerImages[currentIndex]

}

function prevImage(){

    currentIndex--

    if(currentIndex < 0){
        currentIndex = viewerImages.length - 1
    }

    document.getElementById("viewerImage").src = viewerImages[currentIndex]

}

function zoomIn(){
    scale += 0.2
    updateZoom()
}

function zoomOut(){
    scale -= 0.2
    if(scale < 1) scale = 1
    updateZoom()
}

function updateZoom(){

    document
    .getElementById("viewerImage")
    .style.transform = `scale(${scale})`

}

document.addEventListener("keydown",function(e){

    if(document.getElementById("imageViewer").classList.contains("d-none"))
        return

    if(e.key === "ArrowRight") nextImage()
    if(e.key === "ArrowLeft") prevImage()
    if(e.key === "Escape") closeViewer()

})

</script>
@endpush