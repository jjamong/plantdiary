			<!-- common-layer-popup 영역 -->
			<div class="common-layer-popup">
				<!-- 얼랏 팝업(alert) -->
				<div class="alert-layer" id="alert_layer">
					<div class="message-section">
						<p class="message">ㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴㄴ</p>
					</div>
					<div class="btn-section">
						<button type="button" class="btn ok layer-close">확인</button>
					</div>
				</div>
				
				<!-- 컨펌 팝업(confirm) -->
				<div class="confirm-layer" id="confirm_layer">
					<div class="message-section">
						<p class="message"></p>
					</div>
					<div class="btn-section btn-2">
						<div class="btn ok layer-close">확인</div>
						<div class="btn cancel layer-close">취소</div>
					</div>
				</div>

				<!-- 셀렉트 박스 팝업(selectbox) -->
				<div class="selectbox-layer" id="selectbox_layer">
					<div class="list-section">
						<ul class="list"></ul>
					</div>
					<div class="btn-section btn-2">
						<div class="btn ok layer-close">확인</div>
						<div class="btn cancel layer-close">취소</div>
					</div>
				</div>

				<!-- 물주기 간격 설정 레이어 팝업(selectbox) -->
				<!-- <div class="water-interval-layer" id="water_interval_layer">
					<div class="list-section">
						<ul class="list"></ul>
					</div>
					<div class="btn-section btn-2">
						<div class="btn ok layer-close">확인</div>
						<div class="btn cancel layer-close">취소</div>
					</div>
				</div> -->
			</div>
			<!-- // common-layer-popup -->

			<!-- .footer -->
			<div id="footer"></div>
			<!-- // .footer -->
		</div>
		<!-- // Content 영역 -->

		<script src="<?=SITE_URL?>resource/js/dev.app.js"></script>
		<script src="<?=SITE_URL?>resource/js/dev.util.js"></script>
		<script src="<?=SITE_URL?>resource/js/dev.validate.js"></script>
		<script src="<?=SITE_URL?>resource/js/dev.form.js"></script>
		<script src="<?=SITE_URL?>resource/js/common.js"></script>
	</body>
</html>