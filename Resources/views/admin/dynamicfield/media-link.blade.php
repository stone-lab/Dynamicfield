@inject('fileService', 'Modules\Media\Repositories\FileRepository')
<div class="form-group">
	<?php
        $fileId = empty($value) ? 0 : $value;
        $file = $fileService->find($fileId);
     ?>
	<div class="media">
		
		<div class="clearfix"></div>
			{!! Form::hidden($name,$value,["id"=>$id]) !!}
			<a class="btn btn-primary btn-browse" onclick="openCustomMediaWindow(event);" <?php echo isset($file) ? 'style="display:none;"' : '' ?>><i class="fa fa-upload"></i>
				{{ trans('media::media.Browse') }}
			</a>
		<div class="clearfix"></div>
		<figure class="jsThumbnailImageWrapper customMedia">
			@if ($value > 0)
				@if(isset($file))
					<?php $path = $file->path;?>
					<img src="{{ Imagy::getThumbnail($path, 'mediumThumb') }}" alt=""/>
					<a class="jsRemoveSimpleLink" href="#">
						<i class="fa fa-times-circle removeIcon"></i>
					</a>
				@endif
			@endif
		</figure>
	</div>
</div>
<script>
if (typeof window.openCustomMediaWindow === 'undefined') {
	window.mediaPanel = null; 
	window.openCustomMediaWindow = function (event) {
		window.mediaPanel = $(event.currentTarget).closest('.media');
		window.zoneWrapper = $(event.currentTarget).siblings('.jsThumbnailImageWrapper');
		window.open('{!! route("media.grid.select") !!}', '_blank', 'menubar=no,status=no,toolbar=no,scrollbars=yes,height=500,width=1000');
	};
}
if (typeof window.includeMedia === 'undefined') {
	
	window.includeMedia = function (mediaId) {
		$.ajax({
			type: 'POST',
			url: '{{ route('admin.dynamicfield.media.linkMedia') }}',
			data: {
				'mediaId': mediaId,
				'_token': '{{ csrf_token() }}'
			},
			success: function (data) {
				window.mediaPanel.find('input[type=hidden]').first().val(mediaId);
		
				var html = '<img src="' + data.result.thumb + '" alt=""/>' +
						'<a class="jsRemoveSimpleLink" href="#" data-id="' + data.result.imageableId + '">' +
						'<i class="fa fa-times-circle removeIcon"></i>' +
						'</a>';
				window.zoneWrapper.append(html).fadeIn('slow', function() {
					toggleButton($(this));
				});
			}
		});
	};
}
$( document ).ready(function() {
	$('.customMedia.jsThumbnailImageWrapper').off('click', '.jsRemoveSimpleLink');
	$('.customMedia.jsThumbnailImageWrapper').on('click', '.jsRemoveSimpleLink', function (e) {
		e.preventDefault();
		var imageValue = $(this).closest('.media');
		imageValue.find('input[type=hidden]').first().val("");             
		$(e.delegateTarget).fadeOut('slow', function() {
			toggleButton($(this));
		}).html('');
			   
	   
	});
});
function toggleButton(el) {
	var browseButton = el.parent().find('.btn-browse');
	browseButton.toggle();
}
</script>
