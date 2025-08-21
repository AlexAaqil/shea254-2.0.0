<x-app-layout>
@include('partials.navbar')
    <div class="profile_update">
        @include('partials.messages')

        <div class="container page_header">
            <h1>Profile</h1>
        </div>

        <div class="section profile_information">
            <div class="header">
                <h2>Profile Information</h2>
                <p>Update your account's profile information and email address.</p>
            </div>

            <div class="body">
                <div class="custom_form">
                    <form action="{{ route('profile.update') }}" method="post">
                        @csrf
                        @method('patch')

                        <div class="input_group">
                            <label for="first_name">First Name</label>
                            <input type="text" name="first_name" id="first_name" placeholder="First Name" value="{{ old('first_name', $user->first_name) }}">
                            <span class="inline_alert">{{ $errors->first('first_name') }}</span>
                        </div>

                        <div class="input_group">
                            <label for="last_name">Last Name</label>
                            <input type="text" name="last_name" id="last_name" placeholder="Last Name" value="{{ old('last_name', $user->last_name) }}">
                            <span class="inline_alert">{{ $errors->first('last_name') }}</span>
                        </div>

                        <div class="input_group">
                            <label for="email">Email Address</label>
                            <input type="email" name="email" id="email" placeholder="Email Address" value="{{ old('email', $user->email) }}">
                            <span class="inline_alert">{{ $errors->first('email') }}</span>
                        </div>

                        <div class="input_group">
                            <label for="phone_number">Phone Number</label>
                            <input type="text" name="phone_number" id="phone_number" placeholder="Phone Number" value="{{ old('phone_number', $user->phone_number) }}">
                            <span class="inline_alert">{{ $errors->first('phone_number') }}</span>
                        </div>

                        <button type="submit">Update</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="section profile_information">
            <div class="header">
                <h2>Update Password</h2>
                <p>Ensure your account is using a long, random password to stay secure.</p>
            </div>

            <div class="body">
                <div class="custom_form">
                    <form action="{{ route('password.update') }}" method="post">
                        @csrf
                        @method('put')

                        <div class="input_group">
                            <label for="update_password_current_password">Current Password</label>
                            <input type="password" name="current_password" id="update_password_current_password" placeholder="Current Password" value="{{ old('update_password_current_password') }}">
                            <span class="inline_alert">{{ $errors->updatePassword->first('current_password') }}</span>
                        </div>

                        <div class="input_group">
                            <label for="update_password_password">New Password</label>
                            <input type="password" name="password" id="update_password_password" placeholder="New Password" value="{{ old('update_password_password') }}">
                            <span class="inline_alert">{{ $errors->updatePassword->first('password') }}</span>
                        </div>

                        <div class="input_group">
                            <label for="update_password_password_confirmation">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="update_password_password_confirmation" placeholder="Confirm Password" value="{{ old('update_password_password_confirmation') }}">
                            <span class="inline_alert">{{ $errors->updatePassword->first('password_confirmation') }}</span>
                        </div>

                        <button type="submit">Update</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="section delete_account">
        <div class="header">
            <h2>Delete Account</h2>
            <p>Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.</p>
        </div>

        <div class="body">
            <div class="custom_form">
                <form id="deleteAccountForm" action="{{ route('profile.destroy') }}" method="post">
                    @csrf
                    @method('delete')

                    <div class="input_group">
                        <input type="password" id="password" name="password" placeholder="Enter your password to confirm account deletion" required>
                        <span class="inline_alert">{{ $errors->userDeletion->first('password') }}</span>
                    </div>

                    <button id="deleteAccountBtn" type="button" onclick="checkPasswordAndDelete()" class="btn_danger" style="display: none;">Delete Account</button>
                </form>
            </div>
        </div>
    </div>

    <x-sweetalert>
        <script>
            function checkPasswordAndDelete() {
                const password = document.getElementById("password").value.trim();
    
                if (password !== "") {
                    const message = "Are you sure you want to permanently delete your account?";
    
                    // Show a confirmation dialog
                    showConfirmationDialog(message, () => {
                        // Submit the form if the user confirms
                        const form = document.getElementById("deleteAccountForm");
                        if (form) {
                            form.submit();
                        }
                    });
                }
            }
    
            // Add an event listener for the input to toggle the button style
            document.getElementById("password").addEventListener('input', function() {
                const password = this.value.trim();
                const deleteAccountBtn = document.getElementById("deleteAccountBtn");
    
                if (password !== "") {
                    deleteAccountBtn.style.display = "block";
                } else {
                    deleteAccountBtn.style.display = "none";
                }
            });
    
            // Add an event listener for the Enter key press on the password input
            document.getElementById("password").addEventListener('keypress', function(e) {
                if (e.which === 13) { // 13 is the Enter key code
                    e.preventDefault(); // Prevent default form submission
                    checkPasswordAndDelete();
                }
            });
    
            // Add an event listener for the Enter key press on the delete button
            document.getElementById("deleteAccountBtn").addEventListener('keypress', function(e) {
                if (e.which === 13) { // 13 is the Enter key code
                    e.preventDefault(); // Prevent default form submission
                    checkPasswordAndDelete();
                }
            });
        </script>
    </x-jquery>
</x-app-layout>
