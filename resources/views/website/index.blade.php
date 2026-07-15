@extends('website.common.main')
@section('content')
	<!-- Banner Slider Section -->
	<section class="taxi-slider">
		<div id="taxiCarousel" class="carousel slide" data-bs-ride="carousel">
			<!-- Indicators -->
			<div class="carousel-indicators">
				<button type="button" data-bs-target="#taxiCarousel" data-bs-slide-to="0" class="active"></button>
				<button type="button" data-bs-target="#taxiCarousel" data-bs-slide-to="1"></button>
				<button type="button" data-bs-target="#taxiCarousel" data-bs-slide-to="2"></button>
			</div>

			<!-- Slides -->
			<div class="carousel-inner">
				<!-- Slide 1 -->
				<div class="carousel-item active"
					style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');">
					<div class="carousel-caption">
						<h1>Your Ride, Anytime</h1>
						<p>Safe, reliable, and affordable taxi service at your fingertips. Book your ride in seconds and
							travel with confidence.</p>
						<!-- <a href="#" class="btn btn-taxi">Book Now <i class="fas fa-arrow-right ms-2"></i></a> -->
					</div>
				</div>

				<!-- Slide 2 -->
				<div class="carousel-item"
					style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url({{ url('public/website_assets/images/banner-3.jpg') }});">
					<div class="carousel-caption">
						<h1>Your Ride, Anytime</h1>
						<p>Safe, reliable, and affordable taxi service at your fingertips. Book your ride in seconds and
							travel with confidence.</p>
					</div>
				</div>

				<!-- Slide 3 -->
				<div class="carousel-item"
					style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url({{ url('public/website_assets/images/banner-2.jpg') }});">
					<div class="carousel-caption">
						<h1>Your Ride, Anytime</h1>
						<p>Safe, reliable, and affordable taxi service at your fingertips. Book your ride in seconds and
							travel with confidence.</p>
					</div>
				</div>
			</div>

			<!-- Controls -->
			<button class="carousel-control-prev" type="button" data-bs-target="#taxiCarousel" data-bs-slide="prev">
				<span class="carousel-control-prev-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Previous</span>
			</button>
			<button class="carousel-control-next" type="button" data-bs-target="#taxiCarousel" data-bs-slide="next">
				<span class="carousel-control-next-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Next</span>
			</button>
		</div>
	</section>


	<!-- Start home-about Area -->
	<!-- <section class="home-about-area section-gap" style="padding-top: 120px;">
		<div class="container">
			<div class="row section-title pt-5">
				<h1>Your Ride, <span class="red-title">Anytime</span></h1>
				<p>Safe, reliable, and affordable taxi service at your fingertips. <br> Book your ride in seconds and
					travel with confidence.</p>
			</div>
			<div>
				<a href="">
					<button></button>
				</a>
				<a href=""><button></button></a>
			</div>
			<div class="mt-3">
				Available 24/7
				Verified Drivers
				Secure Payments</div>
		</div>
	</section> -->
	<!-- End home-about Area -->

	<!-- Start Choose Area -->
	<section class="services-area mt-5 mb-5 pt-3 pb-3" id="about">
		<div class="container">
			<div class="row section-title pt-5">
				<h1>Why Choose <span class="red-title">Ready Rider?</span></h1>
				<p>Experience the future of transportation with features designed for your convenience and safety.</p>
			</div>
			<div class="row mt-5">
				<div class="col-lg-4 p-3 choose-div-main">
					<div class="choose-div">
						<div class="choose-icon"><i class="bi bi-geo-alt"></i></div>
						<h3 class="mt-5">Real-Time Tracking</h3>
						<p class="mt-3">Track your ride in real-time with live GPS updates and estimated arrival times.
						</p>
					</div>
				</div>
				<div class="col-lg-4 p-3 choose-div-main">
					<div class="choose-div">
						<div class="choose-icon"><i class="bi bi-shield-check"></i></div>
						<h3 class="mt-5">Safe & Secure</h3>
						<p class="mt-3">All drivers are verified and background-checked for your safety and peace of
							mind.</p>
					</div>
				</div>
				<div class="col-lg-4 p-3 choose-div-main">
					<div class="choose-div">
						<div class="choose-icon"><i class="bi bi-credit-card"></i></div>
						<h3 class="mt-5">Easy Payments</h3>
						<p class="mt-3">Multiple payment options including cards, digital wallets, and cash.
						</p>
					</div>
				</div>
			</div>
			<div class="row ">
				<div class="col-lg-4 p-3 choose-div-main">
					<div class="choose-div">
						<div class="choose-icon"><i class="bi bi-clock"></i></div>
						<h3 class="mt-5">24/7 Availability</h3>
						<p class="mt-3">Book rides anytime, anywhere. We're always here when you need us.
						</p>
					</div>
				</div>
				<div class="col-lg-4 p-3 choose-div-main">
					<div class="choose-div">
						<div class="choose-icon"><i class="bi bi-people"></i></div>
						<h3 class="mt-5">Professional Drivers</h3>
						<p class="mt-3">Experienced and courteous drivers committed to excellent service.
						</p>
					</div>
				</div>
				<div class="col-lg-4 p-3 choose-div-main">
					<div class="choose-div">
						<div class="choose-icon"><i class="bi bi-star"></i></div>
						<h3 class="mt-5">Rate Your Ride</h3>
						<p class="mt-3">Share feedback and help us maintain the highest quality standards.
						</p>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- End Choose Area -->
	<!-- Simple & Effortless Start -->
	<section class="services-area mt-5 mb-5 pt-3 pb-3" id="book-ride">
		<div class="container">
			<div class="row section-title pt-5">
				<h1>Simple &<span class="red-title"> Effortless</span></h1>
				<p>Booking your ride has never been easier. Just four simple steps to your destination.</p>
			</div>
		</div>
		<div class="steps-container">

			<div class="step-box">
				<span class="step-number">1</span>
				<div class="step-icon">
					<i class="bi bi-phone"></i>
				</div>
				<h6>Download the App</h6>
				<p>Get RideEasy from the App Store or Google Play Store in seconds.</p>
			</div>

			<div class="step-box">
				<span class="step-number">2</span>
				<div class="step-icon"><i class="bi bi-geo-alt"></i></div>
				<h6>Set Your Destination</h6>
				<p>Enter your pickup and drop-off locations with just a few taps.</p>
			</div>

			<div class="step-box">
				<span class="step-number">3</span>
				<div class="step-icon"><i class="bi bi-car-front"></i></div>
				<h6>Choose Your Ride</h6>
				<p>Select from various vehicle options that match your needs and budget.</p>
			</div>

			<div class="step-box">
				<span class="step-number">4</span>
				<div class="step-icon"><i class="bi bi-check2-circle"></i></div>
				<h6>Enjoy Your Journey</h6>
				<p>Sit back, relax, and enjoy a comfortable ride to your destination.</p>
			</div>

		</div>
	</section>

	<!-- Simple & Effortless End -->
	<!-- Start Contact -->
	<section class="services-area mt-5 mb-5 pt-3 pb-3" id="contact">
		<div class="container">
			<div class="row section-title pt-5">
				<h1>Get In <span class="red-title">Touch</span></h1>
				<p>Have questions? We're here to help. Reach out to our support team anytime.</p>
			</div>
			<div class="row mt-5">
				<div class="col-lg-4 p-3 choose-div-main">
					<div class="choose-div">
						<div class="choose-icon"><i class="bi bi-envelope"></i></div>
						<h5 class="mt-5">Email</h5>
						<p class="mt-3"><a href="">skylite.india@outlook.com</a>
						</p>
					</div>
				</div>
				<div class="col-lg-4 p-3 choose-div-main">
					<div class="choose-div">
						<div class="choose-icon"><i class="bi bi-telephone"></i></div>
						<h5 class="mt-5">Phone</h5>
						<p class="mt-3"><a href="">+91 87876 49393</a></p>
					</div>
				</div>
				<div class="col-lg-4 p-3 choose-div-main">
					<div class="choose-div">
						<div class="choose-icon"><i class="bi bi-building"></i></div>
						<h5 class="mt-5">Office</h5>
						<p class="mt-3" style="font-size: 15px;">C/O SANTOSH ROY, SUBHASH NAGAR, Kanchanpur, North
							Tripura, Tripura, India
						</p>
					</div>
				</div>
			</div>

		</div>
	</section>

@endsection
