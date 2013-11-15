$(function()
{
	var navShow = function (url){
		switch(url)
		{
			case '':
			case '#home':       $('#part_home').show(); $("#part_GroupByDNS").hide();
			break;
			case '#GroupByDNS': $('#part_home').hide(); $("#part_GroupByDNS").show();
			break;
			case '#about':      $('#part_home').hide(); break;
			default:break;
		}
	}

	$("#nav_menu a").on( "click", function(e)
	{
		$("#navbar-nav li").removeClass('active');
		navShow($(this).attr("href"));
		$(this).parent().addClass('active');
		history.pushState({}, '', $(this).attr("href"));

		if( $(this).attr("href")=="#GroupByDNS" )
		{
			if(typeof(oTableGroup)=='undefined')
			{
				var oTableGroup = $('#GroupByDNS').dataTable( {
					"bDestroy": true,
					"bProcessing": true,
					"bServerSide": true,
					"sPaginationType": "full_numbers",
					"iDisplayLength": 100,
					"aLengthMenu": [[100, 250, 500, -1], [100, 250, 500,-1]],
					"aaSorting": [[ 0, "desc" ]],
					"sAjaxSource": "jsonp.php",
					"fnServerData": function( sUrl, aoData, fnCallback, oSettings ) {
						aoData.push( { "name": "GroupeByDNS", "value": "1" } );
						if($('#bannedOnlyGroup:checked').length){
							aoData.push( { "name": "onlyBan", "value": "1" } );
						}
						oSettings.jqXHR = $.ajax( {
							"url": sUrl,
							"data": aoData,
							"success": fnCallback,
							"dataType": "jsonp",
							"cache": false
						} );
					}
				});
				$('#bannedOnlyGroup').on('change',function( event ) {
					oTableGroup.fnReloadAjax();
				});

				var asInitVals2 = new Array();
				$("#part_GroupByDNS tfoot input").on('keyup', function () {
					oTableGroup.fnFilter( this.value, $("#part_GroupByDNS tfoot input").index(this) );
				} );
				$("#part_GroupByDNS tfoot input").each( function (i) {
					asInitVals2[i] = this.value;
				} );
				$("#part_GroupByDNS tfoot input").on('focus', function () {
					if ( this.className == "search_init" )
					{
						this.className = "";
						this.value = "";
					}
				} );
				$("#part_GroupByDNS tfoot input").on('blur', function (i) {
					if ( this.value == "" )
					{
						this.className = "search_init";
						this.value = asInitVals2[$("#part_GroupByDNS tfoot input").index(this)];
					}
				} );
			}
		}
		return false;
	});

	var oTable = $('#dnslist').dataTable( {
		"bDestroy": true,
		"bProcessing": true,
		"bServerSide": true,
		"sPaginationType": "full_numbers",
		"iDisplayLength": 100,
		"aLengthMenu": [[100, 250, 500, -1], [100, 250, 500, "All"]],
		"aaSorting": [[ 0, "desc" ]],
		"sAjaxSource": "jsonp.php",
		"fnServerData": function( sUrl, aoData, fnCallback, oSettings ) {
			if($('#bannedOnly:checked').length){
				aoData.push( { "name": "onlyBan", "value": "1" } );
			}
			oSettings.jqXHR = $.ajax( {
				"url": sUrl,
				"data": aoData,
				"success": fnCallback,
				"dataType": "jsonp",
				"cache": false
			} );
		}
	});
	$('#bannedOnly').on('change',function( event ) {
		oTable.fnReloadAjax();
	});
	var asInitVals = new Array();
	$("#dnslist tfoot input").on('keyup', function () {
		oTable.fnFilter( this.value, $("#dnslist tfoot input").index(this) );
	} );
	$("#dnslist tfoot input").each( function (i) {
		asInitVals[i] = this.value;
	} );
	$("#dnslist tfoot input").on('focus', function () {
		if ( this.className == "search_init" )
		{
			this.className = "";
			this.value = "";
		}
	} );
	$("#dnslist tfoot input").on('blur', function (i) {
		if ( this.value == "" )
		{
			this.className = "search_init";
			this.value = asInitVals[$("#dnslist tfoot input").index(this)];
		}
	} );
	navShow(window.location.hash);
} );