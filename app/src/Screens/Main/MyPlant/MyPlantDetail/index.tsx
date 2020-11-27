import React, {useContext, useEffect, useState} from 'react';
import Styled from 'styled-components/native';
import {useNavigation, useRoute} from '@react-navigation/native';
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
 * MyPlantDetail 내 식물 상세 스크린
 */
let firstLoadCheck = true;  // 처음 로드됬는지 체크

const MyPlantDetail = () => {
    const navigation = useNavigation();
    const router = useRoute();
    
    const {getUserInfo} = useContext(UserContext);
    const {
        webViewUrl,
        headerButton,
        webViewSendMessage,
    } = useContext(ConfigContext);
    
    const [spinner, setSpinner] = useState(false);                      // 스피너 노출 여부
    
    useEffect(() => {
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
    const webViewLoad = async (webview): Promise<void> => {

        let userData = await getUserInfo();
        let myplantSeq = router.params.myplantSeq;
        let message = {
            key : 'webViewLoad',
            data : {
                myplantSeq : myplantSeq,
                userData : userData
            }
        }

        webViewSendMessage(webview, message);
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
            
        // 수정 선택 시
        } else if (key === 'moveMyplantForm') {
            navigation.navigate('MyPlantUpdateForm', {myplantSeq: data.myplantSeq});
            
        // 삭제 완료 후 스크린 이동
        } else if (key === 'moveMyplantList') {
            navigation.navigate('MyPlantList');
        }
    };

    // 헤더 완료 버튼 선택 시
    if (headerButton == 'MyPlantForm') {
        let message = {
            key : 'regist',
                data : {}
        }
        webViewSendMessage(myplantDetailWebview, message);
    }

    // 스피너 노출 / 비노출
    const showSpinner = (): void => {setSpinner(true)};
    const hideSpinner = (): void => {setSpinner(false)};

    return (
        <>
            <WebView
                source={{
                    uri: webViewUrl + '/myplant/detail'
                }}
                onMessage={event => {
                    webViewMessage(event.nativeEvent.data);
                }}
                ref={(ref) => (myplantDetailWebview = ref)}
                onLoadStart={e => showSpinner()}
                onLoadEnd={e => webViewLoad(myplantDetailWebview)}
            />
            {spinner ? <Loading /> : null}
            <BannerContainer>
                <BottomBannerScreen/>
            </BannerContainer>
        </>
    );
};

export default MyPlantDetail;