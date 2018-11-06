<!DOCTYPE html>
<html>
	<head>
		<title>picView: %pagetitle% | D120.de</title>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />

		<link rel="stylesheet" type="text/css" href="%base_uri%/style/d120/node_modules/bootstrap/dist/css/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" href="%base_uri%/style/d120/node_modules/font-awesome/css/font-awesome.min.css" />
		<link rel="stylesheet" type="text/css" href="%base_uri%/style/d120/css/custom.css" />
		<link rel="stylesheet" type="text/css" href="%base_uri%/style/d120/css/tudesign.css" />
		<style>
		#main {
			min-height: 100vh;
			background-color: #333;
			/*padding-top: 54px;*/
		}

		.carousel-inner {
			display: flex;
			align-items: center;
			justify-content: center;
			position: absolute;
			height: 100%;
		}

		#navbar {
			background-color: #000;
			color: #eee;
			line-height: 25px;
		}

		#navbar a:hover, #navbar a:focus {
			color: #aaa;
		}

		#comments {
			padding: 6rem 0;
			/*padding-top: 54px;*/
		}

		#comments footer {
			background-color: transparent;
		}
		</style>
	</head>
	<body class="tud-theme-defs tud-theme-4c">

		<nav id="navbar" class="navbar navbar-default navbar-fixed-top navbar-fix">
			<div class="container-fluid">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a href="/picview" class="navbar-brand">picView</a>
				</div>

				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<div class="navbar-header">
						<div class="nav navbar-text">
							<ol class="breadcrumb" style="margin-bottom: 0;">
								%breadcrumb%
							</ol>
						</div>
					</div>
					<ul class="nav navbar-nav navbar-right">
						%actions%
					</ul>
				</div>
			</div>
		</nav>
		<div id="main" class="carousel">
				%content%
		</div>
		<div id="comments" class="container">
			%comments%
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
		<script>
			var list = localStorage.highlightlist||"";
			$("body").keydown(function(e) {
				left = $(".carousel-control.left").attr("href");
				right = $(".carousel-control.right").attr("href");
				if (e.which==37 && left) location=left;
				else if (e.which==39 && right) location=right;
				else if (e.which==32) {
					addName(location.pathname);
					e.preventDefault();
					return false;
				}
			});
			function addName(str) {
				list+=str+"\n";
				localStorage.highlightlist=list;
				console.log(list);
			}
		</script>
	</body>
</html>
