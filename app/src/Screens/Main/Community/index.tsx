import React, {useEffect} from 'react';
import Styled from 'styled-components/native';

// 스타일
const BannerContainer = Styled.View`flex: 0.1;`
const Container = Styled.View`
    flex: 1;
    width: 100%;
    height: 100%;
    background: #fff;
    alignItems: center;
    justifyContent: center;
`
const Text = Styled.Text``

// 컴포넌트
import BottomBannerScreen from '~/Components/BottomBanner';

/*
 * Community 커뮤니티 스크린
 */
const Community = () => {

    useEffect(() => {
    }, []);

    return (
        <>
            <Container>
                <Text>Comming Soon!</Text>
            </Container>
            <BannerContainer>
                <BottomBannerScreen/>
            </BannerContainer>
        </>
    );
};
export default Community;