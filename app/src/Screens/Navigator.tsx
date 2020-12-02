import React, {useEffect, useContext} from 'react';
import {Platform} from 'react-native'
import Styled from 'styled-components/native';
import {NavigationContainer, useRoute} from '@react-navigation/native';
import {createStackNavigator} from '@react-navigation/stack';
import {createBottomTabNavigator} from '@react-navigation/bottom-tabs';

// 스크린
// 메인 영역
// import CalendarScreen from '~/Screens/Main/Calendar';
import MyPlantListScreen from '~/Screens/Main/MyPlant/MyPlantList';
import MyPlantDetailScreen from '~/Screens/Main/MyPlant/MyPlantDetail';
import MyPlantFormScreen from '~/Screens/Main/MyPlant/MyPlantForm';
import MyPlantDiaryListScreen from '~/Screens/Main/MyPlant/MyPlantDiaryList';
import MyPlantDiaryDetailScreen from '~/Screens/Main/MyPlant/MyPlantDiaryDetail';
import MyPlantDiaryFormScreen from '~/Screens/Main/MyPlant/MyPlantDiaryForm';
import CommunityScreen from '~/Screens/Main/Community';

import SettingScreen from '~/Screens/Setting';
import LoginScreen from '~/Screens/Login/Login';
import JoinScreen from '~/Screens/Login/Join';

// 컴포넌트
import HeaderRight from '~/Components/HeaderRight';

// 스타일 설정
const ContentContainer = Styled.View`flex: 1;`
const Image = Styled.Image`width: 30px; height: 30px;`
const BottomTabText = (Platform.OS === 'ios') ? 
Styled.Text`marginTop: 4px; fontSize:12px;` :
Styled.Text`marginTop: 4px; fontFamily:NanumGothic-Bold; fontSize:12px;`

const Stack = createStackNavigator();
const BottomTab = createBottomTabNavigator();

/*
 * bottomTabBarOptions 바텀 탭 영역 설정
 */
const bottomTabBarOptions = () => {
    return (
        {
            showLabel: false,
                style: {
                height: 60,
                background: 'red',
            },
                tabStyle: {
                height: 60,
                backgroundColor: '#fff',
            },
            activeTintColor: '#00A964',
            inactiveTintColor: '#000',
            keyboardHidesTabBar : true,
        }
    )  
};

/*
 * bottomTabBarOptions 바텀 탭 아이템 영역 설정
 */
const bottomTabBarScreenOptions = (title, screen) => {
    let iconOn;
    let iconOff;

    switch (screen) {
        case 'MyPlantList':
            iconOn = require('~/Assets/Images/plant_icon_on.png')
            iconOff = require('~/Assets/Images/plant_icon_off.png')
            break;
        case 'Calendar':
            iconOn = require('~/Assets/Images/calendar_icon_on.png')
            iconOff = require('~/Assets/Images/calendar_icon_off.png')
            break;
        case 'Community':
            iconOn = require('~/Assets/Images/community_icon_on.png')
            iconOff = require('~/Assets/Images/community_icon_off.png')
            break;
    }

    return (
        {
            tabBarIcon: ({color, focused}) => (
            <>
                <Image source={focused ? iconOn : iconOff} />
                <BottomTabText style = {{color: color}}>
                    {title}
                </BottomTabText>
            </>
            ),
        }
    )
}

/*
 * headerTitleOption 헤더 타이틀 영역 설정
 */
const headerTitleOption = (title, component) => {
    
    let headerTitleStyle = (Platform.OS === 'ios') ? 
    {
        // fontFamily: 'NanumGothic-Bold',
        fontSize: 16,
        color: '#00A964',
        textAlignVertical: 'center',
    } :
    {
        fontFamily: 'NanumGothic-Bold',
        fontSize: 16,
        color: '#00A964',
        textAlignVertical: 'center',
    }

    return (
        {
            title: title,
            headerTitleAlign: 'center',
            headerBackImage: () => <Image source={require('~/Assets/Images/back_icon.png')} />,
            headerBackTitleVisible: false,
            headerStyle: {
                height: 50,
                elevation: 0.5,
            },
            headerTitleStyle: headerTitleStyle,
            headerRight: () => component
        }
    )  
};

/*
 * MainNavigator 메인
 */
const MainNavigator = () => {
    return (
        <BottomTab.Navigator
            //initialRouteName={"Calendar"}
            initialRouteName={"MyPlantList"}
            tabBarOptions={bottomTabBarOptions()}
        >

            {/* 내식물 */}
            <BottomTab.Screen
                name="MyPlantList"
                component={MyPlant}
                options={bottomTabBarScreenOptions('내식물', 'MyPlantList')}
            />

            {/* 캘린더 */}
            <BottomTab.Screen
                name="Calendar"
                component={CommunityScreen}
                options={bottomTabBarScreenOptions('캘린더', 'Calendar')}
            />
            {/* <BottomTab.Screen
                name="Calendar"
                component={Calendar}
                options={bottomTabBarScreenOptions('캘린더', 'Calendar')}
            /> */}

            {/* 커뮤니티 */}
            <BottomTab.Screen
                name="Community"
                component={CommunityScreen}
                options={bottomTabBarScreenOptions('커뮤니티', 'Community')}
            />
        </BottomTab.Navigator>
  );
};

// 내식물
const MyPlant = () => {

    return (
        <Stack.Navigator>
            <Stack.Screen
                name="MyPlantList"
                component={MyPlantListScreen}
                options={{headerShown: false}}
            />
            <Stack.Screen
                name="MyPlantDetail"
                component={MyPlantDetailScreen}
                options={headerTitleOption('식물 상세',
                    <HeaderRight 
                        title={'수정'}
                        screen={'MyPlantDetail'}
                    />
                )}
            />
            <Stack.Screen
                name="MyPlantInsertForm"
                component={MyPlantFormScreen}
                options={headerTitleOption('식물 등록', 
                    <HeaderRight 
                        title={'등록'}
                        screen={'MyPlantInsertForm'}
                    />
                )}
            />
            <Stack.Screen
                name="MyPlantUpdateForm"
                component={MyPlantFormScreen}
                options={headerTitleOption('식물 수정', 
                    <HeaderRight 
                        title={'수정'}
                        screen={'MyPlantUpdateForm'}
                    />
                )}
            />
            <Stack.Screen
                name="MyPlantDiaryList"
                component={MyPlantDiaryListScreen}
                options={headerTitleOption('다이러리')}
            />
            <Stack.Screen
                name="MyPlantDiaryDetail"
                component={MyPlantDiaryDetailScreen}
                options={headerTitleOption('다이러리 상세')}
            />
            <Stack.Screen
                name="MyPlantDiaryInsertForm"
                component={MyPlantDiaryFormScreen}
                options={headerTitleOption('다이러리 등록', 
                    <HeaderRight 
                        title={'등록'}
                        screen={'MyPlantDiaryInsertForm'}
                    />
                )}
            />
            <Stack.Screen
                name="MyPlantDiaryUpdateForm"
                component={MyPlantDiaryFormScreen}
                options={headerTitleOption('다이러리 수정', 
                    <HeaderRight 
                        title={'수정'}
                        screen={'MyPlantDiaryUpdateForm'}
                    />
                )}
            />

    </Stack.Navigator>
  );
};

// 캘린더
const Calendar = () => {
    const router = useRoute();
    let screen;

    // if (router.state) {
    //     screen = router.state.routes[router.state.index].name;
    // }

    return (
        <Stack.Navigator>
            {/* <Stack.Screen
                name="Calendar"
                component={CalendarScreen}
                options={{headerShown: false}}
            />

            <Stack.Screen
                name="MyPlantDetail"
                component={MyPlantDetailScreen}
                options={headerTitleOption('식물 상세')}
            />

            <Stack.Screen
                name="MyPlantUpdateForm"
                component={MyPlantFormScreen}
                options={
                    headerTitleOption('식물 수정', 
                        <HeaderRight 
                            title={'완료'}
                            screen={screen}
                        />
                    )
                }
            /> */}
        </Stack.Navigator>
    );
};

/*
 * Navigator 네비게이터
 */
const Navigator = () => {
    return (
        <ContentContainer>
            <NavigationContainer>
                <Stack.Navigator>
                    <Stack.Screen
                        name="Main"
                        component={MainNavigator}
                        options={{headerShown: false}}
                    />
                    <Stack.Screen
                        name="Setting"
                        component={SettingScreen}
                        options={headerTitleOption('설정')}
                    />
                    <Stack.Screen
                        name="Login"
                        component={LoginScreen}
                        options={headerTitleOption('로그인')}
                    />
                    <Stack.Screen
                        name="Join"
                        component={JoinScreen}
                        options={headerTitleOption('회원가입')}
                    />
                </Stack.Navigator>
            </NavigationContainer>
        </ContentContainer>
    );
};
export default Navigator;