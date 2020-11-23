import React, {createContext, useState, useEffect} from 'react';
import AsyncStorage from '@react-native-community/async-storage';

const defaultContext: userContext = {
  setUserInfo: () => {},
  getUserInfo: () => {},
  delUserInfo: () => {}
}

/*
 * UserContext 컨텍스트
 */
const UserContext = createContext(defaultContext);
interface Props {
  children: JSX.Element | Array<JSX.Element>;
};

/*
 * UserContextProvider 프로바이더
 */
const UserContextProvider = ({children}: Props) => {

  // 회원정보 저장하기
  const setUserInfo = (data: JSON): void => {
    let userInfo = {
      'user_seq' : data.user_seq,
      'user_id' : data.user_id,
    };
    AsyncStorage.setItem('userInfo', JSON.stringify(userInfo));
  };

    // 회원정보 가져오기
    const getUserInfo = async (): Promise<JSON> => {
        let userInfo = await AsyncStorage.getItem('userInfo');
        return JSON.parse(userInfo);
    };

    // 웹뷰로 로그인 정보 전달
    // const webViewSendUserInfo = async (webview): void => {
    //     // 회원정보 가져오기
    //     let userInfo = await getUserInfo();
    //     let userInfoMessage = {
    //         key : 'userInfo',
    //         data : {
    //         userInfo : userInfo
    //         }
    //     }
    //     //console.log('>>> userInfoMessage', userInfoMessage);
    //     if (webview) {
    //         webview.postMessage(JSON.stringify(userInfoMessage));
    //     } else {
    //         console.error('webview 객체가 없습니다.')
    //     }
    // };

  // 회원정보 제거하기
  const delUserInfo = (): void => {
    AsyncStorage.removeItem('userInfo');
  };
  
  useEffect(() => {
  }, []);

  return (
    <UserContext.Provider
      value={{
        setUserInfo,
        getUserInfo,
        delUserInfo,
        //webViewSendUserInfo
      }}>
      {children}
    </UserContext.Provider>
  );
};

export {UserContextProvider, UserContext};