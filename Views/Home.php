<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Decypher</title>
	</head>
	<body>
		<header>
			<h1>Decypher</h1>
		</header>
		<section>
			<article>
				<header>
					<h2>Data Entry</h2>
				</header>
				<p>Enter English here to be converted to an instructions file for BrainJS.</p>
				<form action="/" method="POST">
					<p><textarea name="english" cols="125" rows="25"></textarea></p>
					<p><input type="submit" value="Decypher" /></p>
				</form>
			</article>
			<aside>
				<header>
					<h3>Notes</h3>
				</header>
				<ul>
					<li>Paragraphs and sentences are semantic components of English, so will also be so in this input</li>
					<li>If input is not recognise, consider rephrasing</li>
				</ul>
			</aside>
		</section>
	</body>
</html>