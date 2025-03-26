<x-admin-guest-layout>
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-6">
            <!-- Login -->
            <div class="card">
                <div class="card-body">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center mb-6">
                        <a href="" class="app-brand-link">
                            <span class="app-brand-logo demo">
                                <span class="text-primary">
                                    <svg width="32" height="22" viewBox="0 0 32 22" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z"
                                            fill="currentColor" />
                                        <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                                            d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z"
                                            fill="#161616" />
                                        <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                                            d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z"
                                            fill="#161616" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                            </span>
                            <span class="app-brand-text demo text-heading fw-bold">Vuexy</span>
                        </a>
                    </div>
                    <!-- /Logo -->
                    <h4 class="mb-1">Admin Login</h4>
                    <p class="mb-6">Please sign-in to your account and start the adventure</p>

                    <form id="formAuthentication" class="mb-4" action="{{ route('admin.login') }}" method="post">
                        @csrf
                        <div class="mb-6 form-control-validation">
                            <label for="email" class="form-label">Email or Username</label>
                            <input id="username" type="text" class="form-control
                                @error('username') is-invalid @enderror
                                @error('email') is-invalid @enderror
                            " name="username" autocomplete="off" autofocus>

                            @error('username')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="mb-6 form-password-toggle form-control-validation">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-group input-group-merge">
                                {{-- <input type="password" id="password" class="form-control" name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password" />
                                <span class="input-group-text cursor-pointer"><i
                                        class="icon-base ti tabler-eye-off"></i></span> --}}
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                        name="password" autocomplete="current-password">
                                 @error('password')
                                 <span class="invalid-feedback" role="alert">
                                     <strong>{{ $message }}</strong>
                                 </span>
                                 @enderror
                            </div>
                        </div>
                        <div class="my-8">
                            {{-- <div class="d-flex justify-content-between">
                                <div class="form-check mb-0 ms-2">
                                    <input class="form-check-input" type="checkbox" id="remember-me" />
                                    <label class="form-check-label" for="remember-me"> Remember Me </label>
                                </div>
                                <a href="auth-forgot-password-basic.html">
                                    <p class="mb-0">Forgot Password?</p>
                                </a>
                            </div> --}}
                        </div>
                        <div class="mb-6">
                            <button  class="btn btn-primary d-grid w-100" type="submit">Login</button>
                        </div>
                    </form>

                </div>
            </div>
            <!-- /Login -->
        </div>
    </div>
</x-admin-guest-layout>

