<script>
	_baseURL = "<?php echo CMS\Site::getBaseURL(); ?>";
	onload = function () {
		var input = document.getElementById("page_shortname");
		input.oninput = shortname_preview_update;
		input.onpropertychange = input.oninput;     // for IE8
	};
	function shortname_preview_update () {
		var shortname = document.getElementById("page_shortname").value;
		if (shortname.trim() === "") {
			document.getElementById("shortname_explanation").style.display = "none";
		} else {
			document.getElementById("shortname_explanation").style.display = "block";
		}
		document.getElementById("page_nav_display").innerHTML = shortname;
		document.getElementById("page_url_display").innerHTML = _baseURL + "/" + shortnameToSlug(shortname);

	}
	function shortnameToSlug (shortname) {
		shortname = shortname.replace("-", " ");
		shortname = shortname.trim().toLowerCase();
		shortname = shortname.replace(/[^A-Za-z0-9 ]/g, "");
		shortname = shortname.replace(/ +/g, "-");
		return shortname;
	}
</script>