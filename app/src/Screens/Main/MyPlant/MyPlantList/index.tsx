import React, {useContext, useEffect, useState} from 'react';
import {
    StatusBar,
} from 'react-native';
import Styled from 'styled-components/native';
import {useNavigation} from '@react-navigation/native';
import {WebView} from 'react-native-webview'
//import Modal from 'react-native-modal';

// 컨텍스트
import {ConfigContext} from '~/Context/Config';
import {UserContext} from '~/Context/User';

// 컴포넌트
import BottomBannerScreen from '~/Components/BottomBanner';
import Loading from '~/Components/Loading';

// 스타일 설정
const SafeAreaView = Styled.SafeAreaView`
    height: 100%;
`
const BannerContainer = Styled.View`flex: 0.1;`
const ModalView = Styled.View`
    height: 170px;
`;

/*
 * MyPlant 내식물 스크린
 */
let firstLoadCheck = true;  // 처음 로드됬는지 체크

const MyPlantList = () => {
    const navigation = useNavigation();

    const {getUserInfo} = useContext(UserContext);
    const {
        webViewUrl,
        webViewSendMessage,
        confirmAlert,
    } = useContext(ConfigContext);

    const [spinner, setSpinner] = useState(false);                      // 스피너 노출 여부
    const [selectMyplantSeq, setSelectMyplantSeq] = useState(false);    // 선택한 내식물 순번
    const [modalVisible, setModalVisible] = useState(false);            // 모달 노출 여부
  
    useEffect(() => {
        screenFocus();
    }, []);
  
    // 화면 포커스 시 실행되는 함수
    const screenFocus = (): void => {
        navigation.addListener('focus', () => {
            if (firstLoadCheck) return;
            //webViewLoad();
            myplantListWebview.reload();
        });
    };

    // WebView 호출 완료 후 실행 함수
    const webViewLoad = async (webview): Promise<void> => {
        firstLoadCheck = false;

        let userData = await getUserInfo();
        let message = {
            key : 'webViewLoad',
            data : {
                userData : userData
            }
        }

        // 물주기 모달 창일 경우
        if (webview.props.source.uri.indexOf('procrastina') > -1) {
            message.data.myplantSeq = selectMyplantSeq;
        }
        
        webViewSendMessage(webview, message);
    };

    // WebView 메시지
    const webViewMessage = (response, webview): void => {
        response = JSON.parse(response);
        let key = response.key;
        let data = response.data;

        // 웹뷰 준비 완료
        if (key === 'webViewReady') {
            // 스피너 감추기
            hideSpinner();
        
        // 설정 스크린 이동
        } else if (key === 'moveSetting') {
            navigation.navigate('Setting');

        // 내식물 등록 페이지 이동
        } else if (key === 'myplantForm') {
            navigation.navigate('MyPlantInsertForm');

        // 내식물 상세 페이지 이동
        } else if (key === 'moveMyplantDetail') {
            navigation.navigate('MyPlantDetail', {myplantSeq: data.myplantSeq});

        // 물주기 선택 시
        } else if (key === 'confirmWatering') {
            confirmAlert(data.message, confirmWatering);

        // 미루기 선택 시
        } else if (key === 'showProcrastina') {
            showProcrastina(data.myplantSeq);
            
        // 모달 창 닫기 선택 시
        } if (key === 'closeModal') {
            setModalVisible(false);

        // 로그인 스크린 이동
        } else if (key === 'moveLogin') {
            navigation.navigate('Login', {loginNextScreen: 'MyPlantList'});
        }
    };

    // 물주기 컨펌 확인
    const confirmWatering = () => {
        let message = {
            key : 'confirmWatering',
            data : {}
        }
        webViewSendMessage(myplantListWebview, message);
    };

    // 미루기 선택 시
    const showProcrastina = (myplantSeq) => {
        // 미루기 모달창 보이기
        setSelectMyplantSeq(myplantSeq);
        setModalVisible(true);
    };

    // 스피너 노출 / 비노출
    const showSpinner = (): void => {setSpinner(true)};
    const hideSpinner = (): void => {setSpinner(false)};

    return (
        <>
            <StatusBar barStyle="dark-content" />
            <SafeAreaView>
                <WebView
                    source={{
                        uri: webViewUrl + '/myplant/list'
                    }}
                    onMessage={event => {
                        webViewMessage(event.nativeEvent.data, myplantListWebview);
                    }}
                    ref={(ref) => (myplantListWebview = ref)}
                    onLoadStart={e => showSpinner()}
                    onLoadEnd={e => webViewLoad(myplantListWebview)}
                />
                {spinner ? <Loading /> : null}
                {/* <Modal
                    isVisible={modalVisible}
                    onSwipeComplete={() => setModalVisible(false)}
                    onModalHide={() => {webViewLoad(myplantListWebview)}}
                >
                    <ModalView>
                        <WebView
                            source={{
                                uri: webViewUrl + '/procrastina'
                            }}
                            onMessage={event => {
                                webViewMessage(event.nativeEvent.data, procrastinaWebview);
                            }}
                            ref={(ref) => (procrastinaWebview = ref)}
                            onLoadStart={e => showSpinner()}
                            onLoadEnd={e => webViewLoad(procrastinaWebview)}
                        />
                        {spinner ? <Loading /> : null}
                    </ModalView>
                </Modal> */}
                <BannerContainer>
                    <BottomBannerScreen/>
                </BannerContainer>
            </SafeAreaView>
        </>
    );
};

export default MyPlantList;