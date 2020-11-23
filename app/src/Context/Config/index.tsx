import React, {createContext, useState, useEffect} from 'react';
import {Alert} from 'react-native';

interface Props {
  children: JSX.Element | Array<JSX.Element>;
}

/*
 * ConfigContext 컨텍스트
 */
const defaultContext: configContext = {
  webViewUrl : "http://192.168.35.142", //(개발)
};
const ConfigContext = createContext(defaultContext);

/*
 * ConfigContextProvider 프로바이더
 */
const ConfigContextProvider = ({children}: Props) => {
  
    // 웹뷰
    const webViewUrl = "http://192.168.35.142";

    const [headerButton, setHeaderButton] = useState(String);   // 헤더 버튼 선택
    const [modalVisible, setModalVisible] = useState(false);    // 모달 노출 여부
    const [selectMyplantSeq, setSelectMyplantSeq] = useState();
  
    useEffect(() => {
    }, []);

    // 웹뷰로 정보 전달
    const webViewSendMessage = async (webview, message): void => {
        if (webview) {
            webview.postMessage(JSON.stringify(message));
        } else {
            console.error('webview 객체가 없습니다.', message)
        }
    };

    // 컨펌창
    const confirmAlert = (message, callbackFunction) => {
        Alert.alert('', message,
            [
                {text: '예', onPress: () => callbackFunction()},
                {text: '아니오'},
            ],
            { cancelable: false }
        );
    };

    return (
        <ConfigContext.Provider
            value={{
                webViewUrl,
                headerButton, setHeaderButton,
                modalVisible, setModalVisible,
                selectMyplantSeq, setSelectMyplantSeq,
                webViewSendMessage,
                confirmAlert,
            }}>
            {children}
        </ConfigContext.Provider>
    );
};

export {ConfigContextProvider, ConfigContext};