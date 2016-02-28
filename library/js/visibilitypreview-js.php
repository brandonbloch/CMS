<script>

	var input = document.getElementById("page_visibility");
	input.onchange = visibility_preview_update;
	input.onpropertychange = input.oninput;     // for IE8

	function visibility_preview_update () {
		var visibility = document.getElementById("page_visibility").value;
		var explanation = document.getElementById("visibility_explanation");
		if (visibility == <?php echo CMS\Page::VISIBILITY_PUBLIC; ?>) {
			explanation.innerHTML = "<?php echo CMS\Page::getVisibilityDescriptions()[CMS\Page::VISIBILITY_PUBLIC]; ?>";
		} else if (visibility == <?php echo CMS\Page::VISIBILITY_PRIVATE; ?>) {
			explanation.innerHTML = "<?php echo CMS\Page::getVisibilityDescriptions()[CMS\Page::VISIBILITY_PRIVATE]; ?>";
		} else if (visibility == <?php echo CMS\Page::VISIBILITY_SECRET; ?>) {
			explanation.innerHTML = "<?php echo CMS\Page::getVisibilityDescriptions()[CMS\Page::VISIBILITY_SECRET]; ?>";
		}
	}
</script>