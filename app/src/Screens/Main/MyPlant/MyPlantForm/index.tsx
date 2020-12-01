import React, {useContext, useEffect} from 'react';
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
 * MyPlantForm 내 식물 폼 스크린
 */
let firstLoadCheck = true;  // 처음 로드됬는지 체크

const MyPlantForm = () => {
    const navigation = useNavigation();
    const router = useRoute();
    
    const {getUserInfo} = useContext(UserContext);
    const {webViewUrl, headerButton, webViewSendMessage} = useContext(ConfigContext);
    
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
            
        // 저장 완료 후 리스트 스크린 이동
        } else if (key === 'insertSuccess') {
            navigation.navigate('MyPlantList')
        // 수정 완료 후 리스트 스크린 이동
        } else if (key === 'updateSuccess') {
            navigation.navigate('MyPlantList')
        }
    };

    // 헤더 등록 버튼 선택 시
    if (headerButton == 'MyPlantInsertForm') {
        let message = {
            key : 'insert',
                data : {}
        }
        webViewSendMessage(myplantFormWebview, message);
    // 헤더 수정 버튼 선택 시
    } else if (headerButton == 'MyPlantUpdateForm') {
        let message = {
            key : 'update',
                data : {}
        }
        webViewSendMessage(myplantFormWebview, message);
    }

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
                onLoadEnd={e => webViewLoad(myplantFormWebview)}
                startInLoadingState={true}
                renderLoading={() => <Loading />}
            />
            <BannerContainer>
                <BottomBannerScreen/>
            </BannerContainer>
        </>
    );
};

export default MyPlantForm;