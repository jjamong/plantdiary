import React, {useContext, useEffect, useState} from 'react';
import Styled from 'styled-components/native';
import {useNavigation, useRoute} from '@react-navigation/native';
import {WebView} from 'react-native-webview'
//import DateTimePicker from '@react-native-community/datetimepicker';

// 컨텍스트
import {ConfigContext} from '~/Context/Config';
import {UserContext} from '~/Context/User';

// 컴포넌트
import BottomBannerScreen from '~/Components/BottomBanner';
import Loading from '~/Components/Loading';

// 스타일 설정
const BannerContainer = Styled.View`height:60px`

/*
 * MyPlantForm 내 식물 폼 스크린
 */
let firstLoadCheck = true;  // 처음 로드됬는지 체크

const MyPlantForm = () => {
    const navigation = useNavigation();
    const router = useRoute();
    
    const {getUserInfo} = useContext(UserContext);
    const {webViewUrl, headerButton, webViewSendMessage, confirmAlert} = useContext(ConfigContext);
    
    const [spinner, setSpinner] = useState(false);

    useEffect(() => {
        myplantFormWebview.reload();
        firstLoadCheck = true;
        screenFocus();
    }, []);

    // 화면 포커스 시 실행되는 함수
    const screenFocus = (): void => {
        navigation.addListener('focus', () => {
            if (firstLoadCheck) return;
            // WebView 호출 완료 후 실행 함수
            webViewLoad();
        });
    };

    // WebView 호출 완료 후 실행 함수
    const webViewLoad = async (): Promise<void> => {

        let userData = await getUserInfo();
        let myplantSeq;
        if (router.params) myplantSeq = router.params.myplantSeq;

        let message = {
            key : 'webViewLoad',
            data : {
                myplantSeq : myplantSeq,
                userData : userData
            }
        }
        webViewSendMessage(myplantFormWebview, message);
        firstLoadCheck = false;
    };

    // WebView 메시지
    const webViewMessage = (response): void => {
        response = JSON.parse(response);
        let key = response.key;
        let data = response.data;

        // 웹뷰 준비 완료
        if (key === 'webViewReady') {
            // 스피너 감추기
            hideSpinner();
            
        // 입양일 선택 시
        } else if (key === 'datePickerAdopDate') {
            setShowAdopDate(true);

        // 마지막 물준날 선택 시
        } else if (key === 'datePickerlastWateringDate') {
            setShowLastWateringDate(true);

        // 완료(등록) 선택 시
        } else if (key === 'confirmMyPlantInsert') {
            confirmAlert(data.message, confirmMyPlantInsert);

        // 완료(수정) 선택 시
        } else if (key === 'confirmMyPlantUpdate') {
            confirmAlert(data.message, confirmMyPlantUpdate);

        // 완료 후 스크린 이동
        } else if (key === 'myPlantFormSuccess') {
            navigation.navigate('MyPlantList')
        }
    };

    // 입양일 데이트 피커 선택 시
    // const onChangeAdopDate = (event, selectedDate) => {
    //     setShowAdopDate(Platform.OS === 'ios');

    //     // 확인일 경우
    //     if (event.type == "set") {
    //         setAdopDate(adopDate);
    //         let message = {
    //             key : 'datePickerAdopDateSelect',
    //             data : {
    //                 date : selectedDate
    //             }
    //         }
    //         webViewSendMessage(myplantFormWebview, message);
    //     }
    // };

    // // 마지막 물준날 데이트 피커 선택 시
    // const onChangeLastWateringDate = (event, selectedDate) => {
    //     setShowLastWateringDate(Platform.OS === 'ios');

    //     // 확인일 경우
    //     if (event.type == "set") {
    //         setLastWateringDate(selectedDate);
    //         let message = {
    //             key : 'datePickerLastWateringDateSelect',
    //             data : {
    //                 date : selectedDate
    //             }
    //         }
    //         webViewSendMessage(myplantFormWebview, message);
    //     }
    // };

    // 헤더 완료 버튼 선택 시
    if (headerButton == 'MyPlantInsertForm') {
        let message = {
            key : 'insert',
                data : {}
        }
        webViewSendMessage(myplantFormWebview, message);
    } else if (headerButton == 'MyPlantUpdateForm') {
        let message = {
            key : 'update',
                data : {}
        }
        webViewSendMessage(myplantFormWebview, message);
    }

    // 등록 컨펌 확인
    const confirmMyPlantInsert = () => {
        let message = {
            key : 'confirmMyPlantInsert',
            data : {}
        }
        webViewSendMessage(myplantFormWebview, message);
    };
    
    // 수정 컨펌 확인
    const confirmMyPlantUpdate = () => {
        let message = {
            key : 'confirmMyPlantUpdate',
            data : {}
        }
        webViewSendMessage(myplantFormWebview, message);
    };

    // 스피너 노출
    const showSpinner = (): void => {setSpinner(true)};
    // 스피너 감추기
    const hideSpinner = (): void => {setSpinner(false)};

    return (
        <>
            <WebView
                source={{
                    uri: webViewUrl + '/myplant/form'
                }}
                onMessage={event => {
                    webViewMessage(event.nativeEvent.data);
                }}
                ref={(ref) => (myplantFormWebview = ref)}
                onLoadStart={e => showSpinner()}
                onLoadEnd={e => webViewLoad()}
            />
            {spinner ? <Loading /> : null}

            {/* {showAdopDate && (
                <DateTimePicker
                    testID="adopDatePicker"
                    value={adopDate}
                    mode={'date'}
                    display="spinner"
                    onChange={onChangeAdopDate}
                />
            )}

            {showLastWateringDate && (
                <DateTimePicker
                    testID="lastWateringDatePicker"
                    value={lastWateringDate}
                    mode={'date'}
                    display="spinner"
                    onChange={onChangeLastWateringDate}
                />
            )} */}
            <BannerContainer>
                <BottomBannerScreen/>
            </BannerContainer>
        </>
    );
};

export default MyPlantForm;