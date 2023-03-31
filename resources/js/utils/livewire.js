/**
 * Triggers a page re-render, and resolves when complete.
 *
 * Your Livewire component must have
 * ```
 * protected $listeners = ['render'];
 * ```
 * and
 * ```
 * public function render()
 * {
 *      $this->dispatchBrowserEvent('render');
 * }
 * ```
 * set to work.
 */
export async function render() {
    window.Livewire.emit('render');
    await new Promise(resolve => {
        window.addEventListener('render', resolve, { once: true });
    });
}