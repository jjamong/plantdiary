			<!-- common-layer-popup 영역 -->
			<div class="common-layer-popup">
				<!-- 얼랏 팝업(alert) -->
				<div class="alert-layer" id="alert_layer">
					<div class="message-section">
						<p class="message"></p>
					</div>
					<div class="btn-section btn-1">
						<div class="btn ok layer-close">확인</div>
					</div>
				</div>
				
				<!-- 컨펌 팝업(confirm) -->
				<div class="confirm-layer" id="confirm_layer">
					<div class="message-section">
						<p class="message"></p>
					</div>
					<div class="btn-section btn-2">
						<div class="btn cancel layer-close">취소</div>
						<div class="btn ok layer-close">확인</div>
					</div>
				</div>

				<!-- 셀렉트 박스 팝업(selectbox) -->
				<div class="selectbox-layer" id="selectbox_layer">
					<div class="selectbox-section">
						<div class="list-section">
							<ul class="list"></ul>
						</div>
					</div>
					<div class="btn-section btn-2">
						<div class="btn cancel layer-close">취소</div>
						<div class="btn ok layer-close">확인</div>
					</div>
				</div>

				<!-- 데이트 피커 팝업(datepicker) -->
				<div class="datepicker-layer" id="datepicker_layer">
					<div class="datepicker-section">
						<div class="date">
							<span class="txt-year"></span>년
							<span class="txt-month"></span>월
							<span class="txt-day"></span>일
							<span class="txt-weekday"></span>
						</div>
						<div class="select-section">
							<ul class="year"></ul>
							<ul class="month"></ul>
							<ul class="day"></ul>
						</div>
					</div>
					<div class="btn-section btn-2">
						<div class="btn cancel layer-close">취소</div>
						<div class="btn ok layer-close">확인</div>
					</div>
				</div>
				
				<!-- 돌보기 팝업 -->
				<div class="plant-care-layer" id="plant_care_layer">
					<div class="plant-care-section">
						<form name="plantCareForm" id="plantCareForm" method="post" enctype="multipart/form-data" action="/api/myplant/insert">
							<div class="form-section">
								<div class="plant-care-form-section">
									<div class="item diary-date-section">
										<div class="diary-date"><?= date('Y-m-d') ?></div>
									</div>
									<div class="item water-yn-section">
										<label for="water_yn" class="label">물주기</label>
										<div class="toggle toggle-water">
											<input type="checkbox" name="water_yn" id="water_yn" class="toggle-checkbox" value='Y'>
											<label class="toggle-btn" for="water_yn"><span class="toggle-feature"></span></label>
										</div>
									</div>
									<div class="item soil-condition-yn-section">
										<label for="soil_condition_yn" class="label">흙상태</label>
										<div class="radio-section">
											<div class="input-radio">
												<input type="radio" name="soil_condition_yn" id="soil_condition_yn_1" value="1">
												<label for="soil_condition_yn_1">건조함</label>
											</div>
											<div class="input-radio">
												<input type="radio" name="soil_condition_yn" id="soil_condition_yn_2" checked value="2">
												<label for="soil_condition_yn_2">보통</label>
											</div>
											<div class="input-radio">
												<input type="radio" name="soil_condition_yn" id="soil_condition_yn_3" value="3">
												<label for="soil_condition_yn_3">과습</label>
											</div>
										</div>
									</div>
									<div class="item medicine-yn-section">
										<label for="medicine_yn" class="label">약주기</label>
										<div class="toggle toggle-water">
											<input type="checkbox" name="medicine_yn" id="medicine_yn" class="toggle-checkbox" value='Y'>
											<label class="toggle-btn" for="medicine_yn"><span class="toggle-feature"></span></label>
										</div>
									</div>
									<div class="item pot-replace-yn-section">
										<label for="pot_replace_yn" class="label">분갈이</label>
										<div class="toggle toggle-water">
											<input type="checkbox" name="pot_replace_yn" id="pot_replace_yn" class="toggle-checkbox" value='Y'>
											<label class="toggle-btn" for="pot_replace_yn"><span class="toggle-feature"></span></label>
										</div>
									</div>
									<div class="item diary-img-section">
										<div class="label">이미지</div>
										<div class="image-section">
											<div class="image image1" data-myplant_diary_img_seq="">
												<label for="sys_diary_img1">
													<div class="img"><img src="/resource/images/plant_default_img.jpg" /></div>
												</label>
												<div class="cancel"><img src="/resource/images/cancel_icon.png"></div>
												<div class="input sys_diary_img1"><input type="file" name="sys_diary_img-1" id="sys_diary_img1" placeholder="대표 이미지를 입력해주세요." /></div>
												<div class="msg"></div>
											</div>
											<div class="image image2" data-myplant_diary_img_seq="">
												<label for="sys_diary_img2">
													<div class="img"><img src="/resource/images/plant_default_img.jpg" /></div>
												</label>
												<div class="cancel"><img src="/resource/images/cancel_icon.png"></div>
												<div class="input sys_diary_img2"><input type="file" name="sys_diary_img-2" id="sys_diary_img2" placeholder="대표 이미지를 입력해주세요." /></div>
												<div class="msg"></div>
											</div>
											<div class="image image3" data-myplant_diary_img_seq="">
												<label for="sys_diary_img3">
													<div class="img"><img src="/resource/images/plant_default_img.jpg" /></div>
												</label>
												<div class="cancel"><img src="/resource/images/cancel_icon.png"></div>						
												<div class="input sys_diary_img3"><input type="file" name="sys_diary_img-3" id="sys_diary_img3" placeholder="대표 이미지를 입력해주세요." /></div>
												<div class="msg"></div>
											</div>
										</div>
									</div>
									<div class="item diary-content-section">
										<label for="diary_content" class="label">메모</label>
										<div class="textarea textarea-diary-content">
											<textarea name="diary_content" id="diary_content" placeholder="메모를 입력해주세요."></textarea>
										</div>
										<div class="msg"></div>
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="btn-section btn-2">
						<div class="btn cancel layer-close">취소</div>
						<div class="btn ok layer-close">확인</div>
					</div>
				</div>

				<!-- 회원가입 동의 레이어팝업 -->
				<div class="join-layer" id="join_layer">
					<div class="title-section">
						<div class="title"></div>
					</div>
					<div class="content-section">
						<div class="term"></div>
					</div>
					<div class="btn-section btn-1">
						<div class="btn ok layer-close">확인</div>
					</div>
				</div>
			</div>
			<!-- // common-layer-popup -->

			<!-- .footer -->
			<div id="footer"></div>
			<!-- // .footer -->
		</div>
		<!-- // Content 영역 -->

		<script src="/resource/js/dev.app.js"></script>
		<script src="/resource/js/dev.util.js"></script>
		<script src="/resource/js/dev.validate.js"></script>
		<script src="/resource/js/dev.form.js"></script>
		<script src="/resource/js/dev.layer.js"></script>
		<script src="/resource/js/common.js"></script>
	</body>
</html>