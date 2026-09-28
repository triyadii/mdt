<!DOCTYPE html>
<html lang="en">
	<!--begin::Head-->
	<head>
<base href="../../../" />
		<title>Login - Website MDT</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<!--begin::Fonts(mandatory for all pages)-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
		<!--end::Fonts-->
		<!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
		<link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
		<link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
		<!--end::Global Stylesheets Bundle-->
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="app-blank">
		<!--begin::Theme mode setup on page load-->
		<script>var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>
		<!--end::Theme mode setup on page load-->
		<!--begin::Root-->
		<div class="d-flex flex-column flex-root" id="kt_app_root">
			<!--begin::Authentication - Sign-in -->
			<div class="d-flex flex-column flex-lg-row flex-column-fluid">
				<!--begin::Aside-->
				<div class="d-flex flex-column flex-column-fluid flex-center w-lg-50 p-5 p-lg-10">
					<!--begin::Wrapper-->
					<div class="d-flex justify-content-center flex-column-fluid flex-column w-100 mw-450px">
						
                        <!--begin::Logo-->
                        <div class="text-center mb-10">
                            <img alt="Logo MDT" src="{{ asset('assets/media/logos/logo2.png') }}" class="h-100px h-lg-175px" style="max-width: 100%; object-fit: contain;" />
                        </div>
                        <!--end::Logo-->

						<!--begin::Body-->
						<div class="py-10">
							<!--begin::Form-->
							<form class="form w-100" id="kt_sign_in_form" method="POST" action="{{ route('login') }}">
								@csrf
								<!--begin::Body-->
								<div class="card-body">
									<!--begin::Heading-->
									<div class="text-center mb-10">
										<!--begin::Title-->
										<h1 class="text-gray-900 mb-3 fs-3x">Sign In</h1>
										<!--end::Title-->
										<!--begin::Text-->
										<div class="text-gray-500 fw-semibold fs-6">Sistem Manajemen MDT</div>
										<!--end::Link-->
									</div>
									<!--begin::Heading-->
									<!--begin::Input group=-->
									<div class="fv-row mb-8">
										<!--begin::Username-->
										<input type="text" placeholder="Username" name="username" value="{{ old('username') }}" autocomplete="off" required autofocus class="form-control form-control-solid" />
										<!--end::Username-->
									</div>
									<!--end::Input group=-->
									<div class="fv-row mb-7 position-relative">
										<!--begin::Password-->
										<input type="password" placeholder="Password" name="password" id="password" autocomplete="off" required class="form-control form-control-solid pe-12" />
                                        <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" onclick="togglePasswordVisibility()" style="cursor:pointer; z-index: 10;">
                                            <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                              <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                              <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                            </svg>
                                        </span>
										<!--end::Password-->
									</div>
									<!--end::Input group=-->
                                    
                                    @if ($errors->any())
                                        <div class="alert alert-danger mb-8" style="color: #ba1a1a; background-color: #ffdad6; padding: 1rem; border-radius: 0.25rem;">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
									
									<!--begin::Actions-->
									<div class="d-flex flex-stack justify-content-center mt-5">
										<!--begin::Submit-->
										<button type="submit" id="kt_sign_in_submit" class="btn btn-primary w-100 flex-shrink-0">
											<!--begin::Indicator label-->
											<span class="indicator-label">Masuk Sekarang</span>
											<!--end::Indicator label-->
											<!--begin::Indicator progress-->
											<span class="indicator-progress">
												<span data-kt-translate="general-progress">Please wait...</span>
												<span class="spinner-border spinner-border-sm align-middle ms-2"></span>
											</span>
											<!--end::Indicator progress-->
										</button>
										<!--end::Submit-->
									</div>
									<!--end::Actions-->
								</div>
								<!--begin::Body-->
							</form>
							<!--end::Form-->
						</div>
						<!--end::Body-->
					</div>
					<!--end::Wrapper-->
				</div>
				<!--end::Aside-->
				<!--begin::Body-->
				<div class="d-none d-lg-flex flex-lg-row-fluid w-50 bgi-size-cover bgi-position-y-center bgi-position-x-center bgi-no-repeat justify-content-center align-items-center" style="background-image: url('{{ asset('assets/media/logos/mdt-anim.svg') }}')">
                    <img alt="Logo MDT" src="{{ asset('assets/media/logos/logo2.png') }}" style="max-height: 300px; max-width: 80%; z-index: 10;" />
                </div>
				<!--begin::Body-->
			</div>
			<!--end::Authentication - Sign-in-->
		</div>
		<!--end::Root-->
		<!--begin::Javascript-->
		<script>var hostUrl = "assets/";</script>
		<!--begin::Global Javascript Bundle(mandatory for all pages)-->
		<script src="assets/plugins/global/plugins.bundle.js"></script>
		<script src="assets/js/scripts.bundle.js"></script>
		<!--end::Global Javascript Bundle-->
        <script>
            function togglePasswordVisibility() {
                var passwordInput = document.getElementById("password");
                if (passwordInput.type === "password") {
                    passwordInput.type = "text";
                } else {
                    passwordInput.type = "password";
                }
            }
        </script>
		<!--end::Javascript-->
	</body>
	<!--end::Body-->
</html>