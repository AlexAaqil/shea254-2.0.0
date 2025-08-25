<section class="delete_user">
    <header>
        <h2>Delete Account</h2>
        <p class="text-sm text-gray-600">
            Once your account is deleted, all data will be lost. Please back up anything important.
        </p>
    </header>

    <div class="custom_form">
        <button x-data @click="$dispatch('open-modal', 'confirm-user-deletion')" class="btn_danger">Delete Account</button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <div class="custom_form">
            <form wire:submit="deleteUser" class="p-6">
                <h2 class="text-lg font-semibold text-gray-900">
                    Confirm Deletion
                </h2>

                <p class="mt-2 mb-4 text-sm text-gray-600">
                    Please enter your password to permanently delete your account.
                </p>

                <div class="inputs">
                    <input
                        type="password"
                        wire:model="password"
                        name="password"
                        id="password"
                        class="w-full rounded border-gray-300"
                        placeholder="Password"
                        autofocus
                    >
                    <x-form-input-error field="password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" class="mr-2" x-on:click="$dispatch('close-modal', 'confirm-user-deletion')">
                        Cancel
                    </button>
                    <button type="submit" class="btn_danger">
                        Delete Account
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</section>
