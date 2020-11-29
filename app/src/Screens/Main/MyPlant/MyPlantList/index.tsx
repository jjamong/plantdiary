import React, {useContext, useEffect} from 'react';
import Styled from 'styled-components/native';
import {useNavigation} from '@react-navigation/native';
import {WebView} from 'react-native-webview'
import PushNotification from 'react-native-push-notification'

// 컨텍스트
import {ConfigContext} from '~/Context/Config';
import {UserContext} from '~/Context/User';

// 컴포넌트
import BottomBannerScreen from '~/Components/BottomBanner';
import Loading from '~/Components/Loading';

// 스타일 설정
const BannerContainer = Styled.View`height:60px`

/*
 * MyPlant 내식물 스크린
 */
let firstLoadCheck = true;  // 처음 로드됬는지 체크

const MyPlantList = () => {
    const navigation = useNavigation();

    const {getUserInfo} = useContext(UserContext);
    const {webViewUrl, webViewSendMessage} = useContext(ConfigContext);

    useEffect(() => {
        screenFocus();

        PushNotification.localNotificationSchedule({
            message: "notified",
            date: new Date(Date.now() + 60 * 1000), // in 60 secs
        });
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
        
        webViewSendMessage(webview, message);
    };

    // WebView 메시지
    const webViewMessage = (response): void => {
        response = JSON.parse(response);
        let key = response.key;
        let data = response.data;

        // 웹뷰 준비 완료
        if (key === 'webViewReady') {
        
        // 설정 스크린 이동
        } else if (key === 'moveSetting') {
            navigation.navigate('Setting');

        // 내식물 등록 페이지 이동
        } else if (key === 'myplantForm') {
            navigation.navigate('MyPlantInsertForm');

        // 내식물 상세 페이지 이동
        } else if (key === 'moveMyplantDetail') {
            navigation.navigate('MyPlantDetail', {myplantSeq: data.myplantSeq});

        // 로그인 스크린 이동
        } else if (key === 'moveLogin') {
            navigation.navigate('Login', {loginNextScreen: 'MyPlantList'});
        }
    };

    return (
        <>
            <WebView
                source={{
                    uri: webViewUrl + '/myplant/list'
                }}
                onMessage={event => {
                    webViewMessage(event.nativeEvent.data);
                }}
                ref={(ref) => (myplantListWebview = ref)}
                onLoadEnd={e => webViewLoad(myplantListWebview)}
                startInLoadingState={true}
                renderLoading={() => <Loading />}
            />
            <BannerContainer>
                <BottomBannerScreen/>
            </BannerContainer>
        </>
    );
};

export default MyPlantList;