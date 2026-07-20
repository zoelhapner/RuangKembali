<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">

        <h3 class="fw-bold mb-3">Progress Proyek</h3>

        <div class="timeline-scroll-wrapper">
            <div class="progress-steps-container">

                @foreach($timelineSteps as $index => $step)
                    @php $number = $index + 1; @endphp

                    <div class="step-item" data-step="{{ $step['id'] }}">
                        <div class="step-circle
                            @if($step['completed']) completed
                            @elseif($step['current']) current
                            @endif">
                            {{ $number }}
                        </div>
                        <div class="step-label"
                            data-target="{{ $step['id'] }}">
                            {{ $step['label'] }}
                        </div>
                    </div>
                @endforeach

            </div>
        </div>

    </div>
</div>

<style>
/* WRAPPER SCROLL */
.timeline-scroll-wrapper {
    overflow-x: auto;
    overflow-y: hidden;
    white-space: nowrap;
    padding-bottom: 10px;
    cursor: grab;
}

/* Hide scrollbar (Chrome, Safari, Edge) */
.timeline-scroll-wrapper::-webkit-scrollbar {
    height: 7px;
}
.timeline-scroll-wrapper::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 10px;
}

.progress-steps-container {
    display: inline-flex;
    gap: 40px;
    padding: 10px 5px;
    position: relative;
}

/* ITEM */
.step-item {
    text-align: center;
    position: relative;
    min-width: 80px;
}

/* CIRCLE */
.step-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #adb5bd;
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 auto;
    font-weight: normal;
    position: relative;
    z-index: 2;
    flex-shrink: 0;
}

/* Completed */
.step-circle.completed {
    background: #2fb344;
}

/* Current */
.step-circle.current {
    background: #f59f00;
}

/* LABEL */
.step-label {
    margin-top: 6px;
    font-size: 13px;
    font-weight: 600;
    width: 100px;
    white-space: normal;
    color: #495057;
}

/* CONNECTOR LINES */
.step-item::after {
    content: "";
    position: absolute;
    top: 18px;
    left: calc(100% + 2px);
    width: 40px;
    height: 4px;
    background: #adb5bd;
    z-index: 1;
}

/* Remove last connector */
.step-item:last-child::after {
    display: none;
}

/* Connector for completed */
.step-circle.completed + .step-label + .step-item::after {
    background: #2fb344;
}

</style>

<script>

document.addEventListener('DOMContentLoaded', function () {
    const el = document.querySelector('.timeline-scroll-wrapper');
    let isDown = false;
    let startX;
    let scrollLeft;

    el.addEventListener('mousedown', (e) => {
        isDown = true;
        el.classList.add('active');
        startX = e.pageX - el.offsetLeft;
        scrollLeft = el.scrollLeft;
    });

    el.addEventListener('mouseleave', () => {
        isDown = false;
    });

    el.addEventListener('mouseup', () => {
        isDown = false;
    });

    el.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - el.offsetLeft;
        const walk = (x - startX) * 1; 
        el.scrollLeft = scrollLeft - walk;
    });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll(".step-label").forEach(label => {
        label.addEventListener("click", function (e) {
            e.stopPropagation();

            const targetId = this.dataset.target;
            const target = document.getElementById(targetId);

            if (target) {
                target.scrollIntoView({
                    behavior: "smooth",
                    block: "start"
                });
            }

            // center timeline ke step ini
            this.closest('.step-item')
                .scrollIntoView({ behavior: "smooth", inline: "center" });
        });
    });

});
</script>
