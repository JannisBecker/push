<?php
//Session Stuff
session_start();
if(!isset($_SESSION['initial'])) {
	header("Location: login.php");
}

//Get current directory from GET
$dir = $_GET['dir'];
include("res/php/helpers.php");
?>

<!DOCTYPE html>
<html>
<head>
	<title>push: <?php echo($_SESSION["username"]);?>'s files</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="utf-8" />
	<link rel="stylesheet" href="res/css/style.css">
	<link rel="stylesheet" href="res/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="res/css/dropzone.css">
	<link rel="stylesheet" href="res/css/alertify.core.css">
	<link rel="stylesheet" href="res/css/alertify.default.css">
	<script type="text/javascript" src="res/js/jquery.js"></script>
	<script type="text/javascript" src="res/js/alertify.js"></script>
	<script type="text/javascript" src="res/js/dropzone.js"></script>
	<script type="text/javascript" src="res/js/clipboard.min.js"></script>
</head>
<body>
	<ul class="contextMenu" hidden></ul>
	<div class="upload-zone">
		<form action="upload/index.php" class="dropzone" id="dropzone"></form>
	</div>
	<div class="header" <?php navbar_fixed(); ?>>
		<div class="logo">
			<img class="logo-img" src="res/img/logo.png">
		</div>
		<ul class="menubar">
			<!--<li class="search">
				<input type="search" class="searchbar" onClick="this.setSelectionRange(0, this.value.length)" placeholder="Suche..">
			</li>-->
			<li class="button select"><i class="fa fa-check-circle-o fa-lg"></i> Select</li>
			<li class="button upload"><i class="fa fa-cloud-upload fa-lg"></i> Upload</li>

		</ul>
		<ul class="userbar">
			<li class="button usermenu"><i class="fa fa-cog "></i>
				<span><?php echo($_SESSION['username']); ?></span>
				<ul class="user-dropdown">
					<li class="preferences">Preferences</li>
					<li class="logout">Sign out</li>
				</ul>
			</li>
		</ul>
	</div>

	<div class="breadbar">
		<ul class="breadcrumb">
			<?php generate_breadcrumbs(); ?>
		</ul>
		<ul class="viewmode">
			<li class="view-toggle">View:</li>
			<li class="view-thumbs"><i class="fa fa-th"></i></li>
			<li class="view-list"><i class="fa fa-th-list"></i></li>
		</ul>
	</div>

	<div class="content">
		<ul class="tile-list">
		</ul>
		<div class="loading">
			<img src="res/img/loading.svg">
		</div>
	</div>
</body>
</html>

<script>
	/* Variables */
	var selectionMode = false;
	var listViewMode = <?php echo($_SESSION['prefs']['listview']=="1"?"true":"false"); ?>;
	var uploadMode = false;
	var selectedItems = new Array();
	var lastSelected;
	var clickedItem;
	var selectedAlertItem;

	var loading = false;
	var searchterm;
	var curpage = 0;

	/* General methods */

	/* Open in url in new tab (used for middle mouse clicks) */
	function openNewBgTab(url){
		var a = document.createElement("a");
		a.href = url;
		var evt = document.createEvent("MouseEvent");
		evt.initMouseEvent("click", true, true, window, 0, 0, 0, 0, 0,
			true, false, false, false, 0, null);
		a.dispatchEvent(evt);
	}

	/*  Returns the position of element1 compared to element2 in DOM */
	function comparePos(e1, e2) {
		return (e1.data("index") - e2.data("index"));
	}

	/* Selection */
	function update_selection() {
		selectedItems = new Array();
		$(".tile.file").each(function() {
			if($(this).hasClass("selected")) {
				selectedItems.push($(this));
			}
		});
	}

	function clear_selection() {
		selectedItems = new Array();
		$(".tile.file.selected").each(function() {
			$(this).removeClass("selected");
		});
	}

	function selectionMode_toggle() {
		selectionMode = !selectionMode;
		$("li.button.select").toggleClass("button-active");
		$('.tile.file').toggleClass("selectable");

		if(!selectionMode)
			clear_selection();
	}

	function getSelectedItemNames() {
		var joinedNames = "";
		jQuery.each(selectedItems, function(index, item) {
			joinedNames += item.data("name") + ",";
		});
		return joinedNames.slice(0, -1);
	}

	function hookClipboard() {
		var clip = new Clipboard("li.copyable");
		clip.on('success', function(e) {
	   		alertify.success("Link copied to clipbard!");
		});
	}

	function changeViewMode(doListMode) {
		if(doListMode) {
			$("li.tile").addClass("linetile");

			$("li.view-list").addClass("current");
			$("li.view-thumbs").removeClass("current");
		} else {
			$("li.tile").removeClass("linetile");

			$("li.view-list").removeClass("current");
			$("li.view-thumbs").addClass("current");
		}
		listViewMode = doListMode;
	}

	function loadNextPage() {
		loading = true;
		$("div.loading").css("opacity", 1);
		$.ajax({
			url: "res/php/listfiles.php",
			method: "POST",
			data: { 
				page: curpage+1,
				dir: "<?php echo($dir); ?>",
				listView: listViewMode,
				search: searchterm
			}
		}).done(function(data) {
			if(data.length > 0 && loading) {
				var newData = $('<div class=\"page\" hidden></div>').html(data);
				$("ul.tile-list").append(newData);
				newData.fadeIn();
				loading = false;
				curpage++;
			}
			$("div.loading").css("opacity", 0);
		});
	}
	
	/* Listeners */
	$(document).ready(function() {
		Dropzone.options.dropzone = {
			parallelUploads: 2,
			paramName: "d",
			sending: function(file, xhr, formData) {
				formData.append("k", "warcraft3");
				formData.append("folder", <?php echo("\"".$dir."/\"");?> );
				formData.append("user", <?php echo("\"".$_SESSION['initial']."/\"");?> );
			}
		};

		hookClipboard();

		$('li.view-<?php echo($_SESSION['prefs']['listview']=="1"?"list":"thumbs");?>').addClass("current");
		loadNextPage();
	});

	/* Load next page if scrolled to bottom */
	$(window).scroll(function() {
		if($(window).scrollTop() + $(window).height() > $(document).height() - 150 && !loading) {
			loadNextPage();
	 	}
	});

	/* General Click Listeners */
	$("li.button.select").click(function() {
		selectionMode_toggle();
	});

	$("li.button.upload").click(function() {
		uploadMode = !uploadMode;
		$("div.upload-zone").fadeToggle();
		$("li.button.upload").toggleClass("button-active");
		$("div.header").toggleClass("navbar-fixed");
	});

	$("li.button.usermenu").click(function() {
		$("ul.user-dropdown").slideToggle("fast");
	});

	$("li.preferences").click(function() {
		window.location.href = "preferences.php";
	});

	$("li.logout").click(function() {
		window.location.href = "login.php?logout";
	});

	/* View Mode Selection */
	$("li.view-toggle").click(function() {
		changeViewMode(!listViewMode);
	});

	$("li.view-list").click(function() {
		changeViewMode(true);
	});

	$("li.view-thumbs").click(function() {
		changeViewMode(false);
	});

	/* Link opening / Tile selection */
	$('div.content').on("click","li.tile.file", function(event) {
		if(selectionMode) {
			if(event.shiftKey) {
				var pos = comparePos($(this),lastSelected);
				if(pos != 0) {
					if(pos > 0) {	// this after last
						lastSelected.nextUntil($(this)).filter(":visible").add($(this)).toggleClass("selected");
					} else {	//this before last
						lastSelected.prevUntil($(this)).filter(":visible").add(lastSelected).toggleClass("selected");
					}
				} else $(this).toggleClass("selected");
			} else $(this).toggleClass("selected");
			lastSelected = $(this);
			update_selection();
		} else {
			location.href = "<?php echo("./".$_SESSION["initial"].$dir."/");?>"+$(this).data("name");
		}
	});

	$('div.content').on("mousedown","li.tile.file", function(e){
		if(e.which == 2) {
			openNewBgTab("<?php echo("./".$_SESSION["initial"].$dir."/");?>"+$(this).data("name"));
		}
	});

	/* Folder opening */
	$("div.content").on('click','li.tile.folder', function() {
		location.href = "./?dir=<?php echo($dir);?>/"+$(this).find("span.folder-name").text();
	});

	/* Searching */
	$('input[type=search]').bind('keyup', function() {
		searchterm = $(".searchbar").val().toLowerCase();
		loading = true;
		$.ajax({
			url: "res/php/listfiles.php",
			method: "POST",
			data: { 
				page: 1,
				dir: "<?php echo($dir); ?>",
				search: searchterm
			}
		}).done(function(data) {
			if(data.length > 0) {
				$("ul.tile-list").html(data);
				loading = false;
				curpage = 1;
				console.log("loaded");
			} else {
				$("ul.tile-list").html("<div class=\"wrapped error\">nothing found!</div>");
			}
		});
	});

	$("div.logo").click(function() {
		location.href = "./";
	});

	/* Context Menu Functions and Events */
	function spawnFileMenu(element,x,y) {
		var name = element.data("name");
		if(selectedItems.length > 1) {
			$("ul.contextMenu").append([
				"<li class='menu-name'><span><i class='fa fa-file-o'></i> "+selectedItems.length+" selected files</span></li>",
				"<li class='menu-move file'><span><i class='fa fa-folder-open-o'></i> Move</span></li>",
				"<li class='menu-delete file'><span><i class='fa fa-trash-o'></i> Delete</span></li>"
			]).show().css({top: y + 15, left: x + 10});
		} else {
			var link = "<?php echo($_SERVER['SERVER_NAME']."/".$_SESSION["initial"].$dir."/");?>"+name;
			$("ul.contextMenu").append([
				"<li class='menu-name'><span><i class='fa fa-file-o'></i> "+name+"</span></li>",
				"<li class='menu-copy copyable' data-clipboard-text='"+link+"'><span><i class='fa fa-files-o'></i> Copy Link</span></li>",
				"<li class='menu-rename file'><span><i class='fa fa-pencil-square-o'></i> Rename</span></li>",
				"<li class='menu-move file'><span><i class='fa fa-folder-open-o'></i> Move</span></li>",
				"<li class='menu-delete file'><span><i class='fa fa-trash-o'></i> Delete</span></li>"
			]).show().css({top: y + 15, left: x + 10});
		}
	}

	function spawnFolderMenu(element,x,y) {
		var name = element.find("span.folder-name").text();
		$("ul.contextMenu").append([
			"<li class='menu-name'><span><i class='fa fa-folder-o'></i> "+name+"</span></li>",
			"<li class='menu-rename folder'><span><i class='fa fa-pencil-square-o'></i> Rename</span></li>",
			"<li class='menu-move folder'><span><i class='fa fa-folder-open-o'></i> Move</span></li>",
			"<li class='menu-delete folder'><span><i class='fa fa-trash-o'></i> Delete</span></li>"
		]).show().css({top: y + 15, left: x + 10});
	}

	function spawnDefaultMenu(x,y) {
		$("ul.contextMenu").append([
			"<li class='menu-newfolder'><span><i class='fa fa-folder-o'></i> New Folder</span></li>",
		]).show().css({top: y + 15, left: x + 10});
	}

	$(document).click(function() {
		if ($("ul.contextMenu:hover").length <= 0) {
			$("ul.contextMenu").fadeOut("fast");
		}
	});

	$(document).on("contextmenu", "div.content",function(event) {
			$("ul.contextMenu").html('');
			if(event.pageY > 110)
				spawnDefaultMenu(event.pageX, event.pageY);
				return false;
	});

	$('ul.tile-list').on("contextmenu","li.file", function(event) {
		clickedItem = $(this);
		$("ul.contextMenu").html('');
		spawnFileMenu($(this),event.pageX, event.pageY);
		return false;
	});

	$('ul.tile-list').on("contextmenu", "li.folder", function(event) {
		clickedItem = $(this);
		$("ul.contextMenu").html('');
		spawnFolderMenu($(this),event.pageX, event.pageY);
		return false;
	});

	/* Alertify Item Selection */
	$(document).on('click','li.alertify-item', function() {
		if(selectedAlertItem) {
			selectedAlertItem.removeClass('selected');
		}
		selectedAlertItem = $(this);
		selectedAlertItem.addClass('selected');
	});

	/* New Folder */
	$('ul.contextMenu').on('click','li.menu-newfolder', function() {
		$("ul.contextMenu").fadeOut("fast");
		alertify.prompt("Enter a name for the new folder:", function (e, str) {
			if (e && str != '') {
				$.ajax({
					url: "res/php/folder-actions.php",
					method: "POST",
					data: { 
						action: 'new',
						folder: '<?php echo($dir);?>',
						name: str
					}
				}).done(function(data) {
					if(data == "success") {
						location.reload();
					}
				});
			}
		});
	});

	/* Rename File */
	$('ul.contextMenu').on('click','li.menu-rename.file', function() {
		$("ul.contextMenu").fadeOut("fast");
		var oldName = clickedItem.data('name');
		alertify.prompt("New name for '"+oldName+"':", function (e, str) {
			if (e && str != '' && str != oldName) {
				$.ajax({
					url: "res/php/file-actions.php",
					method: "POST",
					data: { 
						action: 'rename',
						folder: '<?php echo($dir);?>',
						files: oldName,
						name: str
					}
				}).done(function(data) {
					if(data == "success") {
						clickedItem.find("span.file-name").text(str);
						clickedItem.data('name',str);

						var other = clickedItem.find("span.file-other")
						if(other.length) {
							other.text((str.split('.').pop()))
						}

					}
				});
			}
		},oldName);
	});

	/* Move File(s) */
	$('ul.contextMenu').on('click','li.menu-move.file', function() {
		$("ul.contextMenu").fadeOut("fast");

		var folderhtml = '<?php echo(($dir != "")?"<li class=\'alertify-item\'>Home</li>":""); ?>';
		$('li.tile.folder').each(function(index) {
			folderhtml += "<li class='alertify-item'>"+$(this).data('name')+"</li>";
		});

		alertify.confirm("Where to move?"+folderhtml,function(e) {
		if (e) {
			if(selectedAlertItem) {
				$.ajax({
					url: "res/php/file-actions.php",
					method: "POST",
					data: { 
						action: 'move',
						folder: '<?php echo($dir);?>',
						path:  '<?php echo($dir);?>/'+selectedAlertItem.text(),
						root: (selectedAlertItem.text() == "Home"? "true":"false"),
						files: (selectedItems.length > 0 ? getSelectedItemNames() : clickedItem.data('name'))
					}
				}).done(function(data) {
					if(data == "success") {
						if(selectedItems.length > 0) {
							jQuery.each(selectedItems,function(index, item) {
								item.remove();
							});
						} else {
							clickedItem.remove();
						}	
						clear_selection();
					}
				});			
			} else {
				alertify.error('You have to select a folder!');
			}
		}
		selectedAlertItem = null;
		});
	});

	/* Delete File(s) */
	$('ul.contextMenu').on('click','li.menu-delete.file', function() {
		$("ul.contextMenu").fadeOut("fast");
		alertify.confirm("Do you really want to delete these files?", function (e) {
		    if (e) {
		        $.ajax({
				url: "res/php/file-actions.php",
				method: "POST",
				data: { 
					action: 'delete',
					folder: '<?php echo($dir);?>',
					files: (selectedItems.length > 0 ? getSelectedItemNames() : clickedItem.data('name'))
				}
				}).done(function(data) {
					if(data == "success") {
						if(selectedItems.length > 0) {
							jQuery.each(selectedItems,function(index, item) {
								item.remove();
							});
						} else {
							clickedItem.remove();
						}	
						clear_selection();
					}
				});
		    }
		});
	});

	/* Rename Folder */
	$('ul.contextMenu').on('click','li.menu-rename.folder', function() {
		$("ul.contextMenu").fadeOut("fast");
		var oldName = clickedItem.data('name');
		alertify.prompt("New name for '"+oldName+"':", function (e, str) {
			if (e && str != '' && str != oldName && str != 'Home') {
				$.ajax({
					url: "res/php/folder-actions.php",
					method: "POST",
					data: { 
						action: 'rename',
						folder: '<?php echo($dir);?>/'+oldName,
						name: str
					}
				}).done(function(data) {
					if(data == "success") {
						clickedItem.find("span.folder-name").text(str);
						clickedItem.data('name',str);
					}
				});
			}
		},oldName);
	});

	/* Move Folder */
	$('ul.contextMenu').on('click','li.menu-move.folder', function() {
		$("ul.contextMenu").fadeOut("fast");

		var dir = clickedItem.data('name');
		var folderhtml = '<?php echo(($dir != "")?"<li class=\"alertify-item\">Home</li>":""); ?>';
		$('li.tile.folder').each(function(index) {
			var name = $(this).data('name');
			if(name != dir) 
				folderhtml += "<li class='alertify-item'>"+name+"</li>";
		});

		alertify.confirm("Where to move?"+folderhtml,function(e) {
			if (e) {
				if(selectedAlertItem) {
					$.ajax({
						url: "res/php/folder-actions.php",
						method: "POST",
						data: { 
							action: 'move',
							folder: '<?php echo($dir);?>/'+dir,
							path:  '<?php echo($dir);?>/'+selectedAlertItem.text(),
							root: (selectedAlertItem.text() == "Home"? "true":"false")
						}
					}).done(function(data) {
						if(data == "success") {
							clickedItem.remove();
						}
					});
				} else {
					alertify.error('You have to select a folder!');
				}
			}
			selectedAlertItem = null;
		});
	});

	/* Delete Folder */
	$('ul.contextMenu').on('click','li.menu-delete.folder', function() {
		$("ul.contextMenu").fadeOut("fast");
		var dir = clickedItem.data('name');
		alertify.confirm("Do you really want to delete this folder and all of its contents?", function (e) {
			if (e) {
				$.ajax({
					url: "res/php/folder-actions.php",
					method: "POST",
					data: { 
						action: 'delete',
						folder: '<?php echo($dir);?>/'+dir
					}
				}).done(function(data) {
					if(data == "success") {
						clickedItem.remove();
					}
				});
			}
		});	
	});
</script>
