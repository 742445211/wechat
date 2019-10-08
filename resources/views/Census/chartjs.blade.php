<style>
    .all{
        display: flex;
        height: 870px;
        width: 100%;
        flex-wrap: wrap;
        justify-content: space-around;
        background-color: #EEEEEE;
    }
    .census{
        width: 45%;
        height: 410px;
        background-color: #fff;
        display: flex;
        flex-direction: column;
        justify-content: center;
        margin-top: 10px;
    }
    .header{
        display: flex;
        justify-content: space-between;
        width: 80%;
        height: 140px;
        line-height: 140px;
        padding: 0 10%;
        color: #333333;
        font-size: 30px;
    }
    .detail{
        font-weight:400;
        color:rgba(117,117,117,1);
        opacity:0.87;
        font-size: 20px;
    }
    .detail span{
        font-size: 30px;
        color: #333333;
        margin-left: 10px;
    }
    .contents{
        width: 77%;
        height: 270px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 0 13% 0 10%;
        align-content: center;
    }
    .contents>div{
        height: 60px;
        line-height: 60px;
        display: flex;
        justify-content: space-between;
    }
    .title{
        display: flex;
        height: 60px;
        line-height: 60px;
        align-content: center;
    }
    .blue{
        width:19px;
        height:19px;
        background:rgba(255,255,255,1);
        border:5px solid #0000CD;
        border-radius:50%;
        margin-top: 20%;
        margin-right: 10px;
    }
    .yellow{
        width:19px;
        height:19px;
        background:rgba(255,255,255,1);
        border:5px solid #FFD700;
        border-radius:50%;
        margin-top: 17%;
        margin-right: 10px;
    }
    .violet{
        width:19px;
        height:19px;
        background:rgba(255,255,255,1);
        border:5px solid #CD00CD;
        border-radius:50%;
        margin-top: 20%;
        margin-right: 10px;
    }
    .number{
        font-size: 20px;
    }
</style>

<div class="all">
    <div class="census">
        <div class="header">
            <div>岗位统计</div>
            <div class="detail">共计发布过的岗位 <span>{{$work}}</span></div>
        </div>
        <div class="contents">
            <div>
                <div class="title">
                    <div class="blue"></div>
                    <div class="detail">在招职位</div>
                </div>
                <div class="number">
                    {{$recruitment}}
                </div>
            </div>
            <div>
                <div class="title">
                    <div class="yellow"></div>
                    <div class="detail">工作中职位</div>
                </div>
                <div class="number">
                    {{$atwork}}
                </div>
            </div>
            <div>
                <div class="title">
                    <div class="violet"></div>
                    <div class="detail">完成职位</div>
                </div>
                <div class="number">
                    {{$finish}}
                </div>
            </div>
        </div>
    </div>
    <div class="census">
        <div class="header">
            <div>用户统计</div>
            <div class="detail">共计注册了的用户数 <span>{{$registered}}</span></div>
        </div>
        <div class="contents">
            <div>
                <div class="title">
                    <div class="blue"></div>
                    <div class="detail">在职人数</div>
                </div>
                <div class="number">
                    {{$onthejob}}
                </div>
            </div>
            <div>
                <div class="title">
                    <div class="yellow"></div>
                    <div class="detail">未在职人数</div>
                </div>
                <div class="number">
                    {{$registered - $onthejob}}
                </div>
            </div>
            <div></div>
        </div>
    </div>
    <div class="census">
        <div class="header">
            <div>企业统计</div>
            <div class="detail">共计认证过的企业数 <span>{{$business}}</span></div>
        </div>
        <div class="contents">
            <div>
                <div class="title">
                    <div class="blue"></div>
                    <div class="detail">认证企业</div>
                </div>
                <div class="number">
                    {{$certified}}
                </div>
            </div>
            <div>
                <div class="title">
                    <div class="yellow"></div>
                    <div class="detail">未认证企业</div>
                </div>
                <div class="number">
                    {{$uncertified}}
                </div>
            </div>
            <div></div>
        </div>
    </div>
    <div class="census"></div>
</div>
