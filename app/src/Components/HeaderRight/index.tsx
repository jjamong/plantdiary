
import React, {useContext, useEffect} from 'react';
import Styled from 'styled-components/native';

// 컨텍스트
import {ConfigContext} from '~/Context/Config';

// 스타일 설정
const TouchableOpacity = Styled.TouchableOpacity`
    marginRight: 20px;
`;
const Text = Styled.Text`
    color: #00A964;
    fontSize: 14px;
    fontFamily: 'NanumGothic-Bold';
`;

const HeaderRight = (props) => {
    const {setHeaderButton} = useContext<configContext>(ConfigContext);
  
    useEffect(() => {
        console.log(props.screen)
    }, []);

    const onPressAction = () => {
        // set이 바로 두개 연속 있을 경우 set값이 변경되지 않아 setTimeout 코드 추가
        setHeaderButton(props.screen);
        setTimeout(function() {
            setHeaderButton('');
        })
    }

    return (
        <TouchableOpacity
            onPress={onPressAction}
        >
            <Text>{props.title}</Text>
        </TouchableOpacity>
    );
};

export default HeaderRight;