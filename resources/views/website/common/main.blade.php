<!DOCTYPE html>
<html lang="" class="">

<head>
	<!-- Mobile Specific Meta -->
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- Favicon-->
	<link rel="shortcut icon" href="{{ asset('public/website_assets/images/Taxi_logo.png')}}">
	<!-- Author Meta -->
	<meta name="author" content="Archna">
	<!-- Meta Description -->
	<meta name="description" content="">
	<!-- Meta Keyword -->
	<meta name="keywords" content="">
	<!-- meta character set -->
	<meta charset="UTF-8">
	<!-- Site Title -->
	<title>Welcome Ready Rider!</title>

	<link href="https://fonts.googleapis.com/css?family=Poppins:100,200,400,300,500,600,700" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<link rel="stylesheet" href="{{ asset('public/website_assets/css/main.css') }}">
	<link rel="stylesheet" href="{{ asset('public/website_assets/style.css')}}">
</head>

<body>
	<header id="header">
		<nav class="navbar navbar-expand-lg navbar-light bg-black">
			<div class="container-fluid">
				<a class="navbar-brand" href="{{ url('/') }}">
					<img src="{{ asset('public/website_assets/images/Taxi_logo.png')}}" alt="" height="60">
				</a>
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse"
					data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
					aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarSupportedContent">
					<ul class="navbar-nav mx-auto mb-2 mb-lg-0">
						<li class="nav-item">
							<a class="nav-link active" aria-current="page" href="{{ url('/') }}">Home</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="#about">About Us</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="#book-ride">Book Ride</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="#contact">Contact Us</a>
						</li>
					</ul>
					<form class="d-flex">
					<a href="#contact">	<button type="button" class="btn btn-outline-secondary">Contact Support</button></a>
					</form>
				</div>
			</div>
		</nav>
	</header>
	<!-- #header -->
    @yield('content')
	<!-- start footer Area -->
	<footer class="footer-area section-gap">
		<div class="container">
			<div class="row pb-5">
				<div class="col-lg-5 col-md-6 col-sm-6">
					<div class="single-footer-widget">
						<img src="assets/images/Taxi_logo.png" height="90" alt="">
						<div class="p-3">
							<p>Your trusted taxi booking service, providing <br> safe and reliable rides 24/7.</p>
						</div>
					</div>
				</div>
				<div class="col-lg-2 col-md-6 col-sm-6">
					<div class="single-footer-widget">
						<h6>Quick links</h6>
						<ul>
							<li><a href="#about">About</a></li>
							<!--<li><a href="#book-ride">Book Ride</a></li>-->
							<li><a href="#contact">Contact us</a></li>
						</ul>
					</div>
				</div>

				<div class="col-lg-2 col-md-6 col-sm-6 social-widget">
					<div class="single-footer-widget">
						<h6>Legal</h6>
						<ul>
							<li><a href="{{ url('web_privacy_policy') }}">Privacy Policy</a></li>
							<li><a href="{{ url('web_terms_conditions') }}">Terms of Service</a></li>
							<li><a href="{{ url('web_cancel_policy') }}">Cancellation Policy</a></li>
							<li><a href="{{ url('web_refund_policy') }}">Refund Policy</a></li>
						</ul>

					</div>
				</div>
				<div class="col-lg-3  col-md-6 col-sm-6">
					<div class="single-footer-widget">
						<h6>Follow Us</h6>
						<p>Let us be social</p>
						<div class="footer-social d-flex align-items-center">
							<a href="https://www.facebook.com/share/1YchD8a8uw/"><i class="bi bi-facebook"></i></a>
							<a href="https://x.com/SOffcial12473?s=09"><i class="bi bi-twitter"></i></a>
							<a
								href="https://linkedin.com/comm/mynetwork/discovery-see-all?usecase=PEOPLE_FOLLOWS&followMember=skylite-tradeindia-84a08a398"><i
									class="bi bi-linkedin"></i></a>
							<a href="https://www.instagram.com/skylite_tradeindia_official?igsh=ZDM2bWJheWduMWZm"><i
									class="bi bi-instagram"></i></a>
						</div>
					</div>

				</div>

			</div>
			<hr>
			<p class="footer-text col-lg-12">
				© 2025 Ready Rider. All rights reserved.
			</p>
		</div>
	</footer>
	<!-- End footer Area -->
	
	<!-- End Contact -->



	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
		crossorigin="anonymous"></script>
	<script src="js/main.js"></script>
</body>

</html>	