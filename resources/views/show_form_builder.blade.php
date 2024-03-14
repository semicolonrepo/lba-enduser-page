@if ($formBuilder->type === 'text' || $formBuilder->type === 'number')
  <div class="mb-3">
    <label class="form-label" for="{{ $formBuilder->name }}">{{ $formBuilder->label }} : </label>
    <input type="{{ $formBuilder->type }}" name="{{ $formBuilder->name }}" id="{{ $formBuilder->name }}" 
      class="{{ $formBuilder->className }} @if ($errors->has($formBuilder->name)) is-invalid @endif" value="{{ old($formBuilder->name) }}">
    <span class="invalid-feedback">{{ $errors->first($formBuilder->name) }}</span>
  </div>
@endif

@if ($formBuilder->type === 'textarea')
  <div class="mb-3">
    <label class="form-label" for="{{ $formBuilder->name }}">{{ $formBuilder->label }} : </label>
    <textarea id="{{ $formBuilder->name }}" name="{{ $formBuilder->name }}" rows="3"
      class="{{ $formBuilder->className }} @if ($errors->has($formBuilder->name)) is-invalid @endif">{{ old($formBuilder->name) }}</textarea>
    <span class="invalid-feedback">{{ $errors->first($formBuilder->name) }}</span>
  </div>
@endif

@if ($formBuilder->type === 'select')
  <div class="mb-3">
    <label class="form-label" for="{{ $formBuilder->name }}">{{ $formBuilder->label }} : </label>
    <select name="{{ $formBuilder->name }}" id="{{ $formBuilder->name }}" class="{{ $formBuilder->className }} @if ($errors->has($formBuilder->name)) is-invalid @endif">
      <option value="" disabled hidden selected></option>
      @foreach ($formBuilder->values as $option)
        <option value="{{ $option->value }}" {{ $option->selected ? 'selected' : '' }}>{{ $option->label }}</option>
      @endforeach
    </select>
    <span class="invalid-feedback">{{ $errors->first($formBuilder->name) }}</span>
  </div>
@endif

@if ($formBuilder->type === 'checkbox-group')
  <div class="mb-3">
    <label>{{ $formBuilder->label }} : </label>
    <div class="checkbox-group @if ($errors->has($formBuilder->name)) is-invalid @endif">
      @foreach ($formBuilder->values as $option)
        <div class="formbuilder-checkbox">
          <input name="{{ $formBuilder->name }}[]" id="{{ $option->value }}" value="{{ $option->value }}" type="checkbox" 
            {{ ($option->selected || (is_array(old($formBuilder->name)) && in_array($option->value, old($formBuilder->name)))) ? 'checked' : '' }}>
          <label for="{{ $option->value }}">{{ $option->label }}</label>
        </div>
      @endforeach
    </div>
    <span class="invalid-feedback">{{ $errors->first($formBuilder->name) }}</span>
  </div>
@endif

@if ($formBuilder->type === 'starRating' && Route::current()->getName() === 'rating::show')
  <div class="mb-3">
    <label>{{ $formBuilder->label }} : </label>
    <div id="{{ $formBuilder->name }}"></div>
    <input type="hidden" name="{{ $formBuilder->name }}" class="@if ($errors->has($formBuilder->name)) is-invalid @endif">
    <span class="invalid-feedback">{{ $errors->first($formBuilder->name) }}</span>
  </div>
@endif
