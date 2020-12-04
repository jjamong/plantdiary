import React, {useContext, useEffect} from 'react';
import Styled from 'styled-components/native';
import {useNavigation} from '@react-navigation/native';
import {WebView} from 'react-native-webview'

// 컨텍스트
import {ConfigContext} from '~/Context/Config';
import {UserContext} from '~/Context/User';

// 컴포넌트
import BottomBannerScreen from '~/Components/BottomBanner';
import Loading from '~/Components/Loading';

// 스타일 설정
const BannerContainer = Styled.View`height:60px`

/*
 * Setting 설정 스크린
 */
let firstLoadCheck = true;  // 처음 로드됬는지 체크

const Setting = () => {
    const navigation = useNavigation();
    
    const {getUserInfo, delUserInfo} = useContext(UserContext);
    const {webViewUrl, webViewSendMessage, cancelAllLocalNotifications} = useContext(ConfigContext);
    
    useEffect(() => {
        settingWebview.reload();
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

        let message = {
            key : 'webViewLoad',
            data : {
                userData : userData
            }
        }
        webViewSendMessage(settingWebview, message);
        firstLoadCheck = false;
    };

    // WebView 메시지
    const webViewMessage = (response): void => {
        response = JSON.parse(response);
        let key = response.key;
        let data = response.data;

        // 웹뷰 준비 완료
        if (key === 'webViewReady') {

        // 로그인 선택 시
        } else if (key === 'moveLogin') {
            navigation.navigate('Login', {loginNextScreen: 'Setting'});

        // 로그아웃 선택 시
        } else if (key === 'settingLogout') {
            // 알림 전체 삭제
            cancelAllLocalNotifications();

            // 회원정보 제거하기
            delUserInfo();

            // WebView 호출 완료 후 실행 함수
            webViewLoad();
        
        // 회원 탈퇴 시
        } else if (key === 'withdrawalSuccess') {
            // 알림 전체 삭제
            cancelAllLocalNotifications();

            // 회원정보 제거하기
            delUserInfo();
            
            // WebView 호출 완료 후 실행 함수
            webViewLoad();
        }

        
    };

    return (
        <>
            <WebView
                source={{
                    uri: webViewUrl + '/setting'
                }}
                onMessage={event => {
                    webViewMessage(event.nativeEvent.data);
                }}
                ref={(ref) => (settingWebview = ref)}
                onLoadEnd={e => webViewLoad(settingWebview)}
                startInLoadingState={true}
                renderLoading={() => <Loading />}
            />
            <BannerContainer>
                <BottomBannerScreen/>
            </BannerContainer>
        </>
    );
};

export default Setting;