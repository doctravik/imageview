<div class="columns v-cloak--hidden" v-cloak v-show="'{{ session()->has('status') }}'">
    <div class="column is-6 is-offset-3">
        <div class="notification is-primary">{{ session()->get('status') }}</div>
    </div>
</div>