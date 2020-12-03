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
 * Calendar 캘린더 스크린
 */
let firstLoadCheck = true;  // 처음 로드됬는지 체크

const Calendar = () => {
    const navigation = useNavigation();

    const {getUserInfo} = useContext(UserContext);
    const {
        webViewUrl,
        webViewSendMessage,
        setPlantNotification,
    } = useContext(ConfigContext);

    useEffect(() => {
        screenFocus();
    }, []);
  
    // 화면 포커스 시 실행되는 함수
    const screenFocus = (): void => {
        navigation.addListener('focus', () => {
            if (firstLoadCheck) return;
            //webViewLoad();
            calendarWebview.reload();
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

        // 내식물 상세 페이지 이동
        } else if (key === 'moveMyplantDetail') {
            navigation.navigate('MyPlantDetail', {myplantSeq: data.myplantSeq});

        // 다이어리 리스트 스크린 이동
        } else if (key === 'moveMyplantDiaryList') {
            navigation.navigate('MyPlantDiaryList', {myplantSeq: data.myplantSeq});

        // 알림 설정
        } else if (key === 'setNotification') {
            setPlantNotification(data.notificationData);

        // 설정 스크린 이동
        } else if (key === 'moveSetting') {
            navigation.navigate('Setting');

        // 로그인 스크린 이동
        } else if (key === 'moveLogin') {
            navigation.navigate('Login');
            
        }
    };
    
    return (
        <>
            <WebView
                source={{
                    uri: webViewUrl + '/calendar'
                }}
                onMessage={event => {
                    webViewMessage(event.nativeEvent.data);
                }}
                ref={(ref) => (calendarWebview = ref)}
                onLoadEnd={e => webViewLoad(calendarWebview)}
                startInLoadingState={true}
                renderLoading={() => <Loading />}
            />
            <BannerContainer>
                <BottomBannerScreen/>
            </BannerContainer>
        </>
    );
};

export default Calendar;