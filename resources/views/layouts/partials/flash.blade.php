<div v-cloak v-show="'{{ session()->has('status') }}'" class="margin-bottom-2">
    <div class="columns">
        <div class="column is-6 is-offset-3">
            <div class="notification is-primary">{{ session()->get('status') }}</div>
        </div>
    </div>
</div>