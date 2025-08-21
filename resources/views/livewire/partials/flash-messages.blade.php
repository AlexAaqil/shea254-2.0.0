<div
    x-data="{ show: @entangle('show'), message: @entangle('message'), type: @entangle('type') }"
    x-init="$nextTick(() => {
        $watch('show', value => {
            if (value) setTimeout(() => show = false, 5000);
        });
        if (show) setTimeout(() => show = false, 5000);
    })"
    x-show="show"
    x-transition
    class="fixed bottom-6 right-6 z-[9999] px-4 py-3 rounded shadow-lg border"
    :class="{
        'bg-blue-100 border-blue-400 text-blue-800': type === 'status',
        'bg-green-100 border-green-400 text-green-700': type === 'success',
        'bg-red-100 border-red-400 text-red-700': type === 'error',
        'bg-yellow-100 border-yellow-400 text-yellow-800': type === 'warning'
    }"
    style="display: none;"
>
    <span x-text="message"></span>
</div>
