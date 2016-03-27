<script>
	var body = document.getElementsByTagName("body")[0];
	<?php for ($i = 0; $i < CMS\Pages::getCurrentPage()->getZoneCount(); $i++) { ?>
	Sortable.create(document.getElementById("cms-zone-<?php echo $i; ?>"), {
		group: "zones",
		sort: true,
		delay: 0,
		disabled: false,
		animation: 150,

		handle: ".cms-plugin-drag-handle",
		filter: ".cms-plugin-drag-disabled",
		draggable: ".cms-plugin-container",
		ghostClass: "cms-plugin-preview",
		chosenClass: "cms-plugin-dragged",
		dataIdAttr: "data-id",

		scroll: body,
		scrollSensitivity: 100,
		scrollSpeed: 10,

//		setData: function (dataTransfer, dragEl) {
//			Sortable.toggleClass
//		},
//
//		onStart: function (evt) {
//			var index = evt.oldIndex;       // index within parent
//		},
//
//		onEnd: function (evt) {
//			var oldIndex = evt.oldIndex;    // old index within parent
//			var newIndex = evt.newIndex;    // new index within parent
//		},
//
//		onAdd: function (evt) {
//			var item = evt.item;            // dragged HTMLElement
//			var previousList = evt.from;    // previous list
//			var oldIndex = evt.oldIndex;    // old index within parent
//			var newIndex = evt.newIndex;    // new index within parent
//		}
//
//		onUpdate: function (evt) {},
//
//		onSort: function (evt) {},
//
//		onRemove: function (evt) {},
//
//		onFilter: function (evt) {},
//
//		// when moving an item between lists or between lists
//		onMove: function (evt) {}
	});
	<?php } ?>
</script>