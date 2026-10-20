@if ($errors->any())
    <div class="rx-form-errors rx-form-errors-compact" role="alert">
        <div class="rx-form-errors-icon">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>

        <div>
            <strong>برای ادامه، این موارد رو تکمیل کن:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif