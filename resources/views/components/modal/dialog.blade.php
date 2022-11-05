@props(['id' => null, 'maxWidth' => null, 'type' => null])

<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <div wire:ignore.self class="modal fade" id="{{ $type }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div {{ $attributes->merge(['class' => 'modal-dialog']) }}>
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">{{ $title }}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                {{ $content }}
            </div>
            <div class="modal-footer">
                {{ $footer }}
            </div>
          </div>
        </div>
      </div>
</x-modal>