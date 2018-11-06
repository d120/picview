<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>picView: %pagetitle% | D120.de</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" type="text/css" href="%base_uri%/style/d120/node_modules/bootstrap/dist/css/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" href="%base_uri%/style/d120/node_modules/font-awesome/css/font-awesome.min.css" />
		<link rel="stylesheet" type="text/css" href="%base_uri%/style/d120/css/custom.css" />
		<link rel="stylesheet" type="text/css" href="%base_uri%/style/d120/css/tudesign.css" />
		<link rel="stylesheet" type="text/css" href="%base_uri%/style/justifiedGallery.min.css" />
		<style>
			#main {
				min-height: calc(100vh - 326px);
			}
      #sidebar a { padding-top: 2px; padding-bottom: 2px; }
		</style>
	</head>
	<body class="tud-theme-defs tud-theme-7b">
		<div class="container" id="header">
			<a href="https://www.fachschaft.informatik.tu-darmstadt.de">
				<img src="%base_uri%/style/d120/img/d120_logo.png" id="mainLogo" alt="D120: Fachschaft Informatik" class="tud-theme-filled pull-left">
			</a>
			<a href="https://www.tu-darmstadt.de" id="responsivelogo"><img src="%base_uri%/style/d120/img/tu_da_logo.png" alt="Technische Universität Darmstadt" class="pull-right"></a>
		</div>

		<nav id="service-navbar" class="navbar navbar-default tud-theme-filled">
			<div class="container">
				<ul class="header-navbar pull-right nav navbar-nav">
					<li><a href="https://www.fachschaft.informatik.tu-darmstadt.de"><i class="fa fa-home fa-lg"></i> <span class="hidden-xs">Webseite</span></a></li>
					<li><a href="https://daswesentliche.fachschaft.informatik.tu-darmstadt.de"><i class="fa fa-newspaper-o fa-lg"></i> <span class="hidden-xs">dasWESENtliche</span></a></li>
					<li><a href="https://www.fachschaft.informatik.tu-darmstadt.de/forum"><i class="fa fa-comments-o fa-lg"></i> <span class="hidden-xs">Forum</span></a></li>
					<li><a href="https://www.informatik.tu-darmstadt.de"><i class="fa fa-university fa-lg"></i> <span class="hidden-xs">Fachbereich</span></a></li>
				</ul>
			</div>
		</nav>

		<div class="container">
			<div id="main" class="row">
				<div class="col-lg-3 col-md-4 col-sm-5">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#sidebar" aria-expanded="false" aria-controls="sidebar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<ul id="sidebar" class="nav sidebar-nav navbar-collapse collapse tud-theme-colored">
						%navigation%
					</ul>
				</div>
				<div id="content" class="col-lg-9 col-md-8 col-xs-12 col-sm-7">

					<!--<h2>picView: %pagetitle%</h2>-->
					<ol class="breadcrumb">
						%breadcrumb%
					</ol>

					<div id="gallery">
						%content%
					</div>
				</div>
			</div>
		</div>
		<footer class="footer">
			<div class="container">
				<div class="row">
					<div class="col-md-3">
						<p><a href="https://www.fachschaft.informatik.tu-darmstadt.de">Startseite</a></p>
						<p><a href="https://www.fachschaft.informatik.tu-darmstadt.de/kontakt">Kontakt</a></p>
						<p><a href="https://www.fachschaft.informatik.tu-darmstadt.de/impressum">Impressum</a></p>
					</div>
					<div class="col-md-4">
						<p><a href="https://www.openstreetmap.org/node/3893358897#map=17/49.87747639417648/8.654630184173584"><i class="fa fa-map-marker"></i>Hochschulstraße 10, 64289 Darmstadt</a></p>
						<p><a href="tel:+49615116-25522"><i class="fa fa-phone"></i>+49 6151 16-25522</a></p>
						<p><a href="mailto:wir@d120.de"><i class="fa fa-envelope"></i>wir@d120.de</a></p>
					</div>
					<div class="col-md-1">
						<p><a href="https://www.facebook.com/d120.de" aria-label="Facebook"><i class="fa fa-fw fa-facebook" aria-hidden="true" title="Facebook"></i></a></p>
						<p><a href="https://twitter.com/d120de" aria-label="Twitter"><i class="fa fa-fw fa-twitter" aria-hidden="true" title="Twitter"></i></a></p>
						<p><a href="https://github.com/d120" aria-label="Github"><i class="fa fa-fw fa-github" aria-hidden="true" title="Github"></i></a></p>
					</div>
					<div class="col-md-4 bottom-align-text" id="copyright">
						<p>© Fachschaft Informatik TU Darmstadt</p>
					</div>
				</div>
			</div>
		</footer>
		<script src="%base_uri%/style/jquery-2.1.4.min.js"></script>
		<script src="%base_uri%/style/d120/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
		<script src="%base_uri%/style/jquery.justifiedGallery.min.js"></script>
		<script>
			$('.img-gallery').justifiedGallery();
		</script>
	</body>
</html>
