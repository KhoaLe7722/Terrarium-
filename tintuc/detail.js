const url = new URLSearchParams(window.location.search);
const articleId = url.get('id');
const tintuc = [
    {
        id: 3,
        tieude: "Cây cảnh – Tài sản quý giá cho thế hệ sau",
        image: "img__tintuc/3.jpg",
        url: "detail.php?id=3",
        noidung: {
            content1: "Cây cảnh không chỉ có giá trị thẩm mỹ mà còn có thể trở thành tài sản quý giá. Với những cây mai vàng lâu năm, cây kiểng có giá trị, chúng có thể bán được hàng tỷ đồng, là một hình thức đầu tư dài hạn của người nông dân.",
            content__box: [
                {
                    content: `
                    Cây cảnh không chỉ là tài sản quý giá của mỗi gia đình mà còn là di sản thiên nhiên cần được gìn giữ và phát triển để thế hệ mai sau có thể thừa hưởng. Trong bối cảnh nhiều loài cây cảnh quý hiếm đang dần trở nên hiếm hoi vì sự tàn phá của môi trường và những hành vi khai thác quá mức, việc bảo vệ và nhân giống cây cảnh trở nên vô cùng quan trọng. Chúng ta không chỉ cần trồng mới và chăm sóc cây cảnh mà còn phải có các biện pháp bảo tồn, giáo dục cộng đồng về tầm quan trọng của việc duy trì và phát triển các loài cây này. Bằng cách này, cây cảnh sẽ không chỉ là vật trang trí, mà còn là tài sản quý báu, thể hiện sự kính trọng đối với thiên nhiên và truyền thống, góp phần làm phong phú thêm giá trị văn hóa cho các thế hệ tương lai.`,
                    img: "img__tintuc/4.jpg"
                },
                {
                    content: "Cây cảnh từ lâu đã trở thành một phần không thể thiếu trong đời sống của con người, không chỉ mang lại vẻ đẹp thẩm mỹ cho không gian sống mà còn đóng vai trò quan trọng trong việc bảo vệ môi trường. Các loại cây cảnh, đặc biệt là những cây có giá trị văn hóa và lịch sử, được coi là tài sản quý giá, mang đến sự bình yên và hài hòa cho không gian xung quanh. Với sự phát triển của xã hội hiện đại, nhu cầu trồng cây cảnh ngày càng được nhiều người chú trọng không chỉ để trang trí mà còn để bảo tồn những loài cây quý hiếm. Những cây cảnh này không chỉ giúp cải thiện chất lượng không khí mà còn tạo ra một không gian thư giãn, giúp con người thư thái và giảm căng thẳng trong cuộc sống bận rộn.",
                    img: "img__tintuc/8.jpg"
                },
            ],
        },
    },
    {
        id: 4,
        tieude: "Trồng cây kiểng – Nghề truyền thống và tài sản vô giá",
        image: "img__tintuc/4.jpg",
        url: "detail.php?id=4",
        noidung: {
            content1: "Trồng cây kiểng, đặc biệt là các loại cây hoa, cây bonsai, đã trở thành một nghề truyền thống của nhiều gia đình nông dân. Ngoài việc tạo ra các sản phẩm đẹp mắt, cây kiểng còn có giá trị kinh tế lớn trong các dịp lễ hội.",
            content__box: [
                {
                    content: "Cây kiểng không chỉ là một nghề mà còn là tài sản vô giá, là một phần không thể thiếu trong bản sắc văn hóa của dân tộc. Những loại cây kiểng quý giá, như cây cảnh bonsai, cây hoa, hay các giống cây lâu năm, không chỉ mang lại vẻ đẹp cho không gian sống mà còn có giá trị di sản, bảo tồn và phát triển qua các thế hệ. Tuy nhiên, trong bối cảnh hiện nay, nghề trồng cây kiểng đang đối mặt với nhiều thách thức, từ việc đô thị hóa, sự thay đổi khí hậu đến sự lãng quên của giới trẻ đối với nghề truyền thống này. Chính vì vậy, việc bảo tồn và phát triển nghề trồng cây kiểng là vô cùng quan trọng. Không chỉ giúp duy trì nguồn lợi kinh tế cho người dân mà còn giúp giữ gìn những giá trị văn hóa, tinh thần cho các thế hệ sau. Việc truyền dạy nghề trồng cây kiểng cho thế hệ trẻ sẽ là cách tốt nhất để cây kiểng trở thành một tài sản quý giá, góp phần làm phong phú thêm nền văn hóa và cảnh quan Việt Nam trong tương lai.",
                    img: "img__tintuc/12.jpg"
                },
                {
                    content: "Trồng cây kiểng là một nghề truyền thống có từ lâu đời, gắn liền với văn hóa và phong tục của người dân Việt Nam. Nghề này không chỉ đơn thuần là việc trồng cây mà còn bao hàm trong đó sự tinh tế, khéo léo và sự hiểu biết sâu sắc về tự nhiên. Những người làm nghề trồng cây kiểng phải biết cách lựa chọn giống cây phù hợp, chăm sóc cây cẩn thận từ khi gieo trồng cho đến khi phát triển hoàn chỉnh. Cây kiểng không chỉ được trồng để làm đẹp mà còn mang theo những giá trị văn hóa, tinh thần. Trong mỗi chậu cây, người trồng gửi gắm cả sự chăm sóc, tâm huyết và mong muốn tạo ra một sản phẩm không chỉ đẹp mà còn có ý nghĩa sâu sắc. Nghề trồng cây kiểng không chỉ nuôi sống những người làm nghề mà còn góp phần tạo dựng vẻ đẹp cho cảnh quan, giữ gìn truyền thống dân tộc.",
                    img: "img__tintuc/1.jpg"
                },
            ],
        },
    },
    {
        id: 5,
        tieude: "Cây cảnh và phong thủy – Bí quyết làm giàu từ cây kiểng",
        image: "img__tintuc/5.jpg",
        url: "detail.php?id=5",
        noidung: {
            content1: "Cây cảnh không chỉ để trang trí mà còn mang ý nghĩa phong thủy sâu sắc. Cây mai vàng, cây cảnh Tết có thể mang lại may mắn, tài lộc và sức khỏe cho gia đình. Đây là lý do vì sao chúng trở thành món quà quý giá trong dịp Tết.",

            content__box: [
                {
                    content: "Cây cảnh không chỉ có giá trị thẩm mỹ mà còn mang trong mình những ý nghĩa sâu sắc về phong thủy. Trong văn hóa phương Đông, cây kiểng được cho là có khả năng mang lại tài lộc, may mắn và thịnh vượng cho gia chủ. Mỗi loại cây cảnh đều có một ý nghĩa riêng biệt và được chọn lựa cẩn thận để phù hợp với không gian sống cũng như vận mệnh của người sở hữu. Ví dụ, cây phát lộc, cây tiền tài hay cây phú quý được tin là có thể mang lại sự giàu có, thịnh vượng và xua đuổi những năng lượng tiêu cực. Chính vì vậy, việc lựa chọn cây cảnh phù hợp không chỉ giúp không gian trở nên tươi mới mà còn giúp cải thiện phong thủy, thu hút tài lộc và may mắn cho gia đình. Cây cảnh, khi được chăm sóc đúng cách, không chỉ tạo nên vẻ đẹp mà còn là công cụ trong việc cải thiện và duy trì sự cân bằng, hài hòa trong cuộc sống.",
                    img: "img__tintuc/2.jpg"
                },
                {
                    content: "Nghề trồng cây kiểng, đặc biệt là cây phong thủy, đã và đang trở thành một cơ hội kinh doanh tiềm năng cho nhiều người. Việc kết hợp giữa nghệ thuật trồng cây kiểng và kiến thức phong thủy đã tạo ra một xu hướng mới, giúp người trồng không chỉ tạo ra những sản phẩm đẹp mà còn có thể làm giàu từ những giá trị tinh thần mà cây cảnh mang lại. Cây cảnh phong thủy không chỉ là sản phẩm trang trí mà còn là một món quà mang ý nghĩa sâu sắc, giúp người mua giải quyết các vấn đề về tài lộc, sức khỏe và gia đạo. Những người làm nghề trồng cây kiểng biết cách nghiên cứu và áp dụng nguyên lý phong thủy trong việc chăm sóc và tạo hình cây, từ đó cung cấp sản phẩm đáp ứng nhu cầu thị trường. Việc mở rộng thị trường cây cảnh phong thủy, cả trong nước và quốc tế, mang đến cơ hội làm giàu bền vững từ nghề trồng cây kiểng, giúp tạo dựng sự nghiệp lâu dài từ một công việc tưởng chừng như đơn giản nhưng lại ẩn chứa nhiều giá trị vô giá.",
                    img: "img__tintuc/3 .jpg"
                },
            ],
        },
    },
    {
        id: 6,
        tieude: "Trồng cây cảnh trong nhà – Mẹo chăm sóc cây kiểng",
        image: "img__tintuc/6.jpg",
        url: "detail.php?id=6",
        noidung: {
            content1: "Trồng cây cảnh trong nhà không chỉ giúp không gian sống thêm sinh động mà còn giúp làm sạch không khí. Việc chăm sóc cây kiểng đúng cách sẽ giúp cây phát triển khỏe mạnh và giữ được vẻ đẹp lâu dài.",

            content__box: [
                {
                    content: "Để cây cảnh phát triển khỏe mạnh trong môi trường trong nhà, việc chăm sóc đúng cách là vô cùng quan trọng. Một trong những yếu tố quyết định sự sống còn của cây trong nhà là ánh sáng. Mỗi loại cây có nhu cầu ánh sáng khác nhau, vì vậy cần chọn vị trí phù hợp trong nhà để cây nhận đủ ánh sáng mà không bị cháy lá. Các cây ưa bóng râm như cây lưỡi hổ, cây lan ý có thể trồng ở những nơi ít ánh sáng trực tiếp, trong khi các cây ưa sáng như xương rồng, cây nha đam cần được đặt ở gần cửa sổ hoặc nơi có ánh sáng tự nhiên. Bên cạnh đó, việc tưới nước cũng rất quan trọng, vì cây cảnh trong nhà dễ bị úng nếu tưới quá nhiều. Cần kiểm tra độ ẩm của đất và chỉ tưới khi đất thật sự khô. Ngoài ra, việc bón phân định kỳ giúp cung cấp đầy đủ dưỡng chất cho cây, giúp cây phát triển mạnh mẽ và tươi tốt. Việc vệ sinh lá cây để loại bỏ bụi bẩn cũng giúp cây hấp thụ ánh sáng tốt hơn và tránh được bệnh tật.",
                    img: "img__tintuc/9.jpg"
                },
                {
                    content: "Trồng cây cảnh trong nhà không chỉ giúp không gian sống trở nên xanh mát và tươi mới mà còn mang lại nhiều lợi ích cho sức khỏe và tâm lý. Cây cảnh trong nhà có khả năng cải thiện chất lượng không khí bằng cách hút các khí độc như formaldehyde, benzene và toluene, đồng thời tăng cường độ ẩm cho không gian sống, giúp ngôi nhà trở nên trong lành hơn. Ngoài ra, việc trồng cây cảnh còn giúp giảm căng thẳng, tăng sự tập trung và cải thiện tâm trạng, tạo ra một không gian thư giãn và dễ chịu. Cây kiểng cũng đóng vai trò quan trọng trong phong thủy, mang lại sự hài hòa và tài lộc cho gia chủ. Chính vì vậy, trồng cây cảnh trong nhà không chỉ là sở thích mà còn là một cách chăm sóc sức khỏe tinh thần và thể chất cho các thành viên trong gia đình.",
                    img: "img__tintuc/6.jpg"
                },
            ],
        },
    },
    {
        id: 7,
        tieude: "Những loại cây bonsai đẹp và dễ trồng",
        image: "img__tintuc/7.jpg",
        url: "detail.php?id=7",
        noidung: {
            content1: "Cây bonsai là một trong những loại cây cảnh được ưa chuộng nhất hiện nay. Với kỹ thuật cắt tỉa và chăm sóc tỉ mỉ, cây bonsai có thể trở thành tác phẩm nghệ thuật đẹp mắt. Những cây bonsai đẹp còn có thể bán với giá cao, trở thành nguồn thu nhập cho nhiều người trồng.",

            content__box: [
                {
                    content: "Bonsai là nghệ thuật tạo dáng cây cảnh thu nhỏ, bắt nguồn từ Nhật Bản nhưng đã trở nên phổ biến trên toàn thế giới. Đặc trưng của bonsai là việc chăm sóc và tạo hình cây sao cho chúng có dáng vẻ tự nhiên nhưng vẫn giữ được tỷ lệ nhỏ gọn, hợp lý. Bonsai không chỉ mang lại vẻ đẹp thẩm mỹ mà còn thể hiện sự kiên nhẫn và tài nghệ của người trồng. Các loại cây bonsai thường có thể sống lâu dài nếu được chăm sóc đúng cách, và việc trồng bonsai trong nhà hoặc sân vườn giúp tạo ra không gian thư giãn, hài hòa. Dưới đây là một số loại cây bonsai đẹp và dễ trồng, phù hợp cho những người mới bắt đầu hoặc những ai yêu thích nghệ thuật bonsai.",
                    img: "img__tintuc/14.jpg"
                },
                {
                    content: `Cây Tùng:
Cây tùng là một trong những loại cây bonsai phổ biến và dễ trồng. Với thân cây cứng cáp và lá kim nhỏ, tùng tạo ra một vẻ đẹp mạnh mẽ, sang trọng. Cây tùng rất dễ chăm sóc và có thể sống lâu trong điều kiện khí hậu Việt Nam, đặc biệt là những nơi có khí hậu mát mẻ. Cây tùng thích hợp trồng trong các chậu bonsai và có thể chịu được ánh sáng yếu, thích hợp cho cả trong nhà và ngoài trời. <br>

Cây Mai: <br>
Cây mai là một lựa chọn phổ biến trong nghệ thuật bonsai nhờ vào sắc hoa vàng rực rỡ vào mùa xuân. Cây mai bonsai có thể dễ dàng được tạo dáng và chịu được khí hậu nhiệt đới. Chăm sóc cây mai không khó, chỉ cần tưới nước đều đặn và bảo vệ cây khỏi ánh sáng mặt trời quá gắt trong mùa hè. Cây mai bonsai còn mang lại ý nghĩa phong thủy, tượng trưng cho sự thịnh vượng và tài lộc.<br>

Cây Ficus (Cây Gai):<br>
Cây ficus là một lựa chọn phổ biến cho những người mới bắt đầu chơi bonsai vì nó rất dễ chăm sóc và chịu được nhiều điều kiện khác nhau. Ficus có lá xanh mướt và dễ tạo hình, làm cho nó trở thành lựa chọn lý tưởng cho những ai muốn có một cây bonsai đẹp mà không tốn quá nhiều công sức chăm sóc. Cây ficus thích ánh sáng mạnh nhưng có thể sống tốt trong bóng râm nhẹ.<br>

Cây Lộc Vừng:<br>
Lộc vừng là một loài cây bonsai đẹp với những chùm hoa đỏ rực rỡ. Loại cây này dễ trồng và có thể phát triển tốt trong môi trường nóng ẩm, rất phù hợp với khí hậu Việt Nam. Lộc vừng bonsai mang lại vẻ đẹp nổi bật và ý nghĩa phong thủy về sự phát tài, may mắn.<br>

Cây Đào:<br>
Cây đào bonsai nổi bật với những bông hoa đào tươi sáng vào mùa xuân. Nó không chỉ mang vẻ đẹp dịu dàng mà còn tượng trưng cho sự sinh sôi, phát triển và thịnh vượng. Cây đào khá dễ trồng và chăm sóc, thích hợp với những người mới bắt đầu yêu thích bonsai. Cây đào thường được trồng trong chậu nhỏ và có thể tạo hình đẹp mắt khi ra hoa.<br>`,
                    img: "img__tintuc/13.jpg"
                },
            ],
        },
    },
    {
        id: 8,
        tieude: "Cây cảnh Tết – Một phần không thể thiếu trong mỗi gia đình",
        image: "img__tintuc/8.jpg",
        url: "detail.php?id=8",
        noidung: {
            content1: "Cây cảnh Tết không chỉ là phần trang trí cho ngôi nhà mà còn mang theo ý nghĩa phong thủy trong năm mới. Những cây mai vàng, cây quất, cây đào là những cây không thể thiếu trong mỗi gia đình trong dịp Tết Nguyên Đán, tượng trưng cho sự may mắn và tài lộc.",
            content__box: [
                {
                    content: "Trong văn hóa Việt Nam, cây cảnh Tết không chỉ đơn thuần là vật trang trí mà còn mang những ý nghĩa sâu sắc, tượng trưng cho sự phát tài, thịnh vượng và an lành. Mỗi dịp Tết đến, việc trưng bày cây cảnh trong nhà đã trở thành một phong tục không thể thiếu. Những loại cây đặc trưng như mai, đào, quất, hay cây lộc vừng không chỉ giúp không gian trở nên tươi mới mà còn mang đến những lời chúc tốt đẹp cho một năm mới hạnh phúc, an khang. Cây cảnh Tết mang theo hy vọng, sự khởi đầu mới, và là sự kết nối giữa con người với thiên nhiên. Vì vậy, mỗi gia đình thường lựa chọn các loại cây hợp với phong thủy, giúp gia đình đón nhận tài lộc, may mắn trong năm mới.",
                    img: "img__tintuc/1.jpg"
                },
                {
                    content: "Có một số loại cây cảnh Tết phổ biến mà mỗi gia đình đều lựa chọn trong dịp Tết Nguyên Đán để tạo không gian ấm cúng và may mắn. Cây đào là biểu tượng của mùa xuân, với những đóa hoa màu hồng hoặc đỏ rực rỡ, mang lại không khí Tết ấm áp và tươi vui. Cây đào còn tượng trưng cho sự sinh sôi nảy nở, phát triển mạnh mẽ. Cây mai lại là loài cây đặc trưng của miền Nam, với những bông hoa vàng tươi thắm, mang ý nghĩa phú quý, thịnh vượng và thành đạt. Cây quất cũng là một lựa chọn phổ biến trong mỗi gia đình vào dịp Tết, với quả tròn, vàng óng ánh tượng trưng cho sự sung túc và đủ đầy. Không chỉ đẹp mắt, những cây cảnh này còn góp phần tạo nên không khí lễ hội, làm cho không gian sống thêm phần tươi mới và tràn ngập năng lượng tích cực, giúp gia đình khởi đầu một năm mới thuận lợi và hạnh phúc.",
                    img: "img__tintuc/8.jpg"
                },
            ],
        },

    },
    {
        id: 9,
        tieude: "Chăm sóc cây bonsai – Nghệ thuật trồng cây kiểng",
        image: "img__tintuc/9.jpg",
        url: "detail.php?id=9",
        noidung: {
            content1: "Chăm sóc cây bonsai là một nghệ thuật đòi hỏi sự kiên nhẫn và tỉ mỉ. Mỗi cây bonsai có yêu cầu về môi trường sống, ánh sáng, nước và phân bón khác nhau. Để cây bonsai đẹp và phát triển tốt, cần phải có sự chăm sóc kỹ lưỡng và đúng cách.",

            content__box: [
                {
                    content: "Chăm sóc cây bonsai là một nghệ thuật đòi hỏi sự kiên nhẫn, tinh tế và kiến thức vững về các yếu tố như ánh sáng, đất, nước và không khí. Không giống như các loại cây thông thường, bonsai yêu cầu người trồng phải biết cách tạo dáng, cắt tỉa, uốn nắn để tạo ra những cây cảnh thu nhỏ nhưng vẫn giữ được vẻ đẹp tự nhiên và cân đối. Việc lựa chọn giống cây bonsai phù hợp với điều kiện sống cũng rất quan trọng. Mỗi loại cây có nhu cầu về ánh sáng và nhiệt độ khác nhau, vì vậy người trồng phải hiểu rõ đặc điểm của từng cây để chăm sóc đúng cách. Ngoài ra, việc cắt tỉa cây định kỳ không chỉ giúp cây phát triển đều mà còn làm tăng tính thẩm mỹ, mang lại vẻ đẹp độc đáo cho từng cây bonsai.",
                    img: "img__tintuc/14.jpg"
                },
                {
                    content: "Để cây bonsai phát triển khỏe mạnh, việc chăm sóc đúng cách là rất quan trọng. Đầu tiên, cây cần được đặt ở nơi có ánh sáng phù hợp; đa số các cây bonsai đều ưa thích ánh sáng tự nhiên nhưng không chịu được ánh sáng trực tiếp quá mạnh, đặc biệt vào mùa hè. Tiếp theo, việc tưới nước cũng cần được chú ý; cây bonsai yêu cầu đất luôn được giữ ẩm vừa phải, không quá khô nhưng cũng không bị ngập úng. Để tránh tình trạng này, người trồng có thể kiểm tra độ ẩm của đất bằng tay trước khi tưới. Bên cạnh đó, bón phân cho cây bonsai là một yếu tố không thể thiếu, giúp cây có đủ dưỡng chất để phát triển. Tùy thuộc vào từng loại cây, người trồng có thể sử dụng phân hữu cơ hoặc phân hóa học phù hợp. Cắt tỉa và uốn nắn là hai công việc không thể thiếu trong quá trình chăm sóc bonsai. Việc này giúp cây có hình dáng gọn gàng, đẹp mắt và phát triển đều đặn theo ý muốn của người trồng. Bằng sự kiên nhẫn và yêu thích, nghệ thuật chăm sóc cây bonsai sẽ mang lại những tác phẩm cây cảnh ấn tượng, vừa đẹp mắt vừa có ý nghĩa phong thủy tốt lành.",
                    img: "img__tintuc/13.jpg"
                },
            ],
        },
    },
    {
        id: 10,
        tieude: "Giới thiệu các loại cây kiểng phổ biến",
        image: "img__tintuc/10.jpg",
        url: "detail.php?id=10",
        noidung: {
            content1: "Có rất nhiều loại cây kiểng được ưa chuộng hiện nay, từ các loại cây bonsai, cây hoa cho đến cây mai vàng, cây quất. Mỗi loại cây có đặc điểm và cách chăm sóc riêng, phù hợp với từng không gian và nhu cầu của gia chủ.",

            content__box: [
                {
                    content: "Cây kiểng không chỉ mang lại vẻ đẹp thẩm mỹ cho không gian sống mà còn giúp cải thiện chất lượng không khí, tạo không gian xanh mát và thư giãn. Những loại cây kiểng phổ biến hiện nay rất đa dạng, phù hợp với nhiều mục đích sử dụng từ trang trí trong nhà, sân vườn đến những cây có giá trị phong thủy. Các loại cây kiểng này thường dễ chăm sóc và phát triển tốt trong điều kiện khí hậu nhiệt đới, làm phong phú thêm môi trường sống và tạo cảm giác dễ chịu cho con người. Dưới đây là một số loại cây kiểng phổ biến mà nhiều gia đình, văn phòng hay khuôn viên công cộng yêu thích trồng.",
                    img: "img__tintuc/3.jpg"
                },
                {
                    content: `Cây Lưỡi Hổ (Sansevieria):
Cây lưỡi hổ là một trong những loại cây kiểng phổ biến vì tính dễ chăm sóc và khả năng phát triển mạnh mẽ trong điều kiện thiếu sáng. Cây có hình dáng lá dài, nhọn như lưỡi kiếm và có màu sắc đặc trưng với các vệt sọc xanh vàng. Lưỡi hổ còn mang ý nghĩa phong thủy tốt, giúp gia chủ đón tài lộc, xua đuổi tà khí và mang lại sự bình an.<br>

<br>Cây Bàng Singapore:
<br>Bàng Singapore là một loại cây kiểng dễ trồng trong nhà và được ưa chuộng vì lá to, dày và xanh mướt, mang lại vẻ đẹp tươi mới cho không gian sống. Cây phát triển tốt trong bóng râm và không yêu cầu quá nhiều ánh sáng mặt trời. Đặc biệt, cây còn có khả năng thanh lọc không khí rất tốt, giúp không gian sống trở nên trong lành.

<br>Cây Phát Tài (Crassula Ovata):
<br>Cây phát tài hay còn gọi là cây tiền tài, là loài cây mang lại ý nghĩa phong thủy đặc biệt. Cây có hình dáng nhỏ gọn, với những lá tròn, xanh bóng và thường được trồng trong các chậu nhỏ để trang trí bàn làm việc, góc phòng. Theo quan niệm phong thủy, cây phát tài giúp gia chủ thu hút tài lộc, phát đạt và thịnh vượng.

<br>Cây Cẩm Nhung (Fittonia):
<br>Cẩm nhung là cây kiểng nổi bật với những chiếc lá mềm mại và màu sắc đẹp mắt, từ xanh đến đỏ hoặc trắng, tùy theo loại. Cây này thích hợp với các không gian nhà có ánh sáng yếu, rất dễ chăm sóc và phù hợp với những người mới bắt đầu trồng cây. Cẩm nhung cũng có tác dụng làm dịu không khí, giúp tạo không gian thư giãn cho gia đình.

<br>Cây Hoa Mai:
<br>Cây hoa mai không chỉ đẹp mà còn mang giá trị văn hóa cao trong mỗi dịp Tết Nguyên Đán. Những bông hoa mai vàng rực rỡ tượng trưng cho sự may mắn, tài lộc và sự khởi đầu mới. Cây có thể trồng trong chậu hoặc ngoài vườn, và với việc chăm sóc hợp lý, cây sẽ nở hoa vào mùa xuân, mang lại không khí tươi mới và phấn khởi cho ngôi nhà.

<br>Cây Cọ Nhật:
<br>Cọ Nhật là một trong những loại cây kiểng dễ trồng và phát triển trong điều kiện bóng râm, rất thích hợp để trang trí trong nhà. Cây có thân thẳng và lá dài, nhọn, tạo thành một dáng vẻ uyển chuyển, thanh thoát. Cọ Nhật thường được trồng trong chậu lớn và rất thích hợp để trưng bày trong các không gian rộng, giúp không gian sống thêm phần sang trọng.`,
                    img: "img__tintuc/12.jpg"
                },
            ],
        },
    },
    {
        id: 11,
        tieude: "Cây kiểng cho không gian nội thất hiện đại",
        image: "img__tintuc/11.jpg",
        url: "detail.php?id=11",
        noidung: {
            content1: "Cây kiểng không chỉ giúp làm đẹp không gian mà còn có thể tạo điểm nhấn cho ngôi nhà của bạn. Cây như cây kim tiền, cây lưỡi hổ hay cây phú quý được ưa chuộng trong các không gian nội thất hiện đại nhờ vào khả năng thích ứng với môi trường trong nhà và dễ dàng chăm sóc.",

            content__box: [
                {
                    content: "Trong không gian nội thất hiện đại, cây kiểng không chỉ đơn thuần là vật trang trí mà còn là yếu tố quan trọng giúp tạo ra một không gian sống xanh, thoải mái và dễ chịu. Với sự phát triển của xu hướng thiết kế nội thất hiện đại, cây kiểng đã trở thành một phần không thể thiếu trong việc tạo nên vẻ đẹp thẩm mỹ và sự hài hòa cho căn nhà. Những loại cây kiểng phù hợp với phong cách hiện đại thường có hình dáng đơn giản, lá xanh mướt và dễ chăm sóc, không chiếm quá nhiều không gian. Chúng giúp làm mềm hóa những góc cạnh khô khan, tạo sự tươi mới cho không gian sống và mang lại bầu không khí trong lành, giúp gia chủ giảm bớn căng thẳng và thư giãn trong cuộc sống hằng ngày.",
                    img: "img__tintuc/3.jpg"
                },
                {
                    content: `Cây Lưỡi Hổ (Sansevieria):
<br>Lưỡi hổ là một trong những loại cây kiểng lý tưởng cho không gian nội thất hiện đại. Với hình dáng đơn giản, lá dài và cứng cáp, cây lưỡi hổ mang lại vẻ đẹp thanh thoát và phù hợp với những không gian trang trí theo phong cách tối giản. Đặc biệt, cây lưỡi hổ còn có khả năng lọc không khí tốt, giúp không gian trong lành hơn, rất phù hợp cho các văn phòng, phòng khách hoặc phòng ngủ.

<br>Cây Xương Rồng (Cactus):
<br>Xương rồng không chỉ là loại cây dễ chăm sóc mà còn rất thích hợp với những không gian nội thất hiện đại, đặc biệt là các không gian có xu hướng thiết kế tối giản. Cây xương rồng có dáng vẻ độc đáo và ít cần chăm sóc, chỉ cần ánh sáng vừa phải và tưới nước thỉnh thoảng. Xương rồng thích hợp trang trí trên bàn làm việc hoặc kệ sách, mang lại một điểm nhấn tự nhiên nhưng không quá rườm rà cho không gian.

<br>Cây Cọ Nhật (Rhapis Excelsa):
<br>Cọ Nhật là một cây kiểng phù hợp với không gian nội thất hiện đại nhờ vào dáng vẻ thanh thoát và tươi mát. Cây có thân thẳng và lá dài, mảnh, dễ dàng trở thành điểm nhấn trong các không gian rộng như phòng khách hoặc sảnh của các căn hộ. Cọ Nhật không yêu cầu quá nhiều ánh sáng và có thể sống tốt trong điều kiện bóng râm nhẹ, thích hợp cho các căn hộ đô thị.

<br>Cây Trầu Bà (Pothos):
<br>Cây trầu bà là loại cây dễ trồng và chăm sóc, rất phù hợp cho không gian nội thất hiện đại, đặc biệt là với những người yêu thích cây cảnh treo hoặc bò trên kệ. Cây trầu bà có lá xanh bóng, tươi mát và có thể dễ dàng tạo dáng theo ý thích. Loại cây này không chỉ giúp không gian sống trở nên sinh động mà còn giúp cải thiện chất lượng không khí trong nhà.

<br>Cây Cẩm Nhung (Fittonia):
<br>Cây cẩm nhung nổi bật với những chiếc lá mềm mại và màu sắc đẹp mắt, từ xanh đến đỏ hoặc trắng, rất phù hợp với các không gian nội thất hiện đại có phong cách nhẹ nhàng, tinh tế. Cẩm nhung có thể trồng trong những chậu nhỏ hoặc chậu treo, làm cho không gian trở nên tươi mới và thu hút ánh nhìn. Cây này phát triển tốt trong điều kiện bóng râm nhẹ và không yêu cầu nhiều ánh sáng.

<br>Cây Phát Lộc (Crassula Ovata):
<br>Cây phát lộc với lá tròn và màu xanh bóng, thường được trồng trong chậu nhỏ và dễ dàng chăm sóc, rất thích hợp với những không gian nội thất hiện đại có phong cách tối giản. Đây cũng là một loài cây mang ý nghĩa phong thủy tốt, tượng trưng cho tài lộc, may mắn và thịnh vượng, tạo thêm không khí tích cực cho căn nhà.

<br>Những loại cây kiểng này không chỉ dễ chăm sóc mà còn rất thích hợp để trang trí cho không gian nội thất hiện đại. Chúng giúp không gian sống trở nên sinh động hơn, tạo điểm nhấn ấn tượng và mang lại không khí trong lành, tươi mới cho ngôi nhà.`,
                    img: "img__tintuc/4.jpg"
                },
            ],
        },
    },
    {
        id: 12,
        tieude: "Thuận Phát Garent có 1 bài đăng mới",
        image: "img__tintuc/12.jpg",
        url: "detail.php?id=12",
        noidung: {
            content1: `🌱 Rừng Mơ, một khu vườn nhỏ nhưng đầy mộng mơ, nơi cây kiểng và rêu xanh mọc lên một cách tự nhiên, bình yên. Cây kiểng không chỉ là vật trang trí mà còn mang đến cho chúng ta cảm giác thư thái, giúp xua tan mọi lo âu, căng thẳng trong cuộc sống. Tại Rừng Mơ, những cây kiểng được chăm sóc tỉ mỉ, tạo nên không gian tuyệt vời để bạn hòa mình vào thiên nhiên, tìm lại sự bình yên trong tâm hồn.

<br>✨ Rừng Mơ không chỉ là một chiếc terrarium, mà là nơi hội tụ của những cây kiểng đẹp, mang lại không gian sống xanh mát, giúp bạn tạm quên đi những bộn bề ngoài kia. Hãy đến và thưởng thức không gian yên bình này, nơi những cây kiểng bé nhỏ nhưng lại chứa đựng cả một thế giới tươi mới, trong lành.`,
            content__box: [
                {
                    content: `🌿 Trong giấc mơ mùa hè có một khu rừng nhỏ đang thì thầm những lời thơ…<br>
                       ✨Chúng mình gọi đó là Rừng Mơ – một chiếc terrarium, rêu vẫn mọc êm đềm như chưa từng biết đến những vội vã, cỏ nhỏ vẫn xanh dù thế giới ngoài kia có chuyển mùa bao nhiêu lần.<br>
                       🌱 Nếu bạn cũng đang cần một khoảng xanh dịu để mơ giữa ngày, hãy ghé thăm Rừng Mơ – nơi trú ngụ của những giấc mơ mùa hè bé xíu mà trong lành.<br>
                       🏡 Hệ thống cửa hàng:           <br>                                   
                       ▪️ 𝐂𝐡𝐢 𝐧𝐡𝐚́𝐧𝐡 2: Quán nước Hoa Yên, 131 Lý Tự Trọng, P. An Phú, Q. Ninh Kiều, TP. Cần Thơ. (7:30 - 21:30).   <br>                   
                       ▪️ 𝐂𝐡𝐢 𝐧𝐡𝐚́𝐧𝐡 3: Tổ Của Yến Coffee, số 13, đường Trần Ngọc Quế, P Xuân Khánh, Q Ninh Kiều, TP Cần Thơ.           <br>                             
                       ▪️ 𝐂𝐡𝐢 𝐧𝐡𝐚́𝐧𝐡 4: Tiệm Cà Phê Khoảnh Khắc, Số 11/9, đường Trần Bình Trọng, P An Phú, Q Ninh Kiều, TP Cần Thơ. <br>
                       ▪️ 𝐂𝐡𝐢 𝐧𝐡𝐚́𝐧𝐡 5: Ngọc Trương Coffee, 372B Đ. Nguyễn Văn Cừ, Phường An Khánh, Ninh Kiều, Cần Thơ<br>
                       ▪️ 𝐂𝐡𝐢 𝐧𝐡𝐚́𝐧𝐡 6: Ngọc Trương Coffee In Box, 387 - 389 Đ. Nguyễn Văn Cừ, An Hoa, Ninh Kiều, Cần Thơ<br>
                       ▪️ Chi nhánh 7: Trường Cao Đẳng Tây Đô, 90 QL1A, Ba Láng, Cái Răng, Cần Thơ<br>
                       ▪️ 𝐊𝐡𝐮 𝐭𝐫𝐮̛𝐧𝐠 𝐛𝐚̀𝐲: Charmant Suites a Boutique Hotel Can Tho, số 45 đường Ngô Quyền, P Tân An, Q Ninh Kiều, TP Cần Thơ.   <br>
                       ▪️ 𝗣𝗵𝗼̀𝗻𝗴 𝘁𝗵𝗶𝗲̂́𝘁 𝗸𝗲̂́: số 141 đường B3, khu dân cư Hưng Phú, Q Cái Răng, TP Cần Thơ. (Hẹn khách khi thiết kế theo yêu cầu)<br>`,
                    img: "img__tintuc/12a.jpg"
                },
                {
                    content: "   ",
                    img: "img__tintuc/12b.jpg"
                },
            ],
        },
    },
    {
        id: 13,
        tieude: "🌿 Thuận Phát Garden – Cây Kiểng Đẹp, Thiết Kế Sáng Tạo Giữa Lòng Cần Thơ",
        image: "img__tintuc/13.jpg",
        url: "detail.php?id=13",
        noidung:
        {
            content1: `Trong nhịp sống hiện đại, khi con người ngày càng khao khát tìm lại sự bình yên và kết nối với thiên nhiên, cây kiểng không chỉ đơn thuần là một thú chơi, mà còn là nghệ thuật – nơi tinh hoa của tự nhiên gặp gỡ bàn tay tài hoa của người làm vườn. Thuận Phát Garden, với sứ mệnh "mang thiên nhiên vào không gian sống", đã và đang tạo nên dấu ấn riêng tại Cần Thơ bằng những thiết kế cây cảnh độc đáo, sáng tạo và đầy cảm xúc.`,
            content__box: [
                {
                    content: `Tại Thuận Phát Garden, mỗi chậu cây, mỗi khóm bonsai, mỗi tiểu cảnh đều được thổi hồn bằng tư duy thẩm mỹ hiện đại nhưng vẫn giữ nét truyền thống Việt. Không đơn điệu với kiểu dáng thông thường, cây kiểng tại đây được thiết kế theo chủ đề, bố cục hài hòa giữa thế dáng – màu sắc – và chất liệu chậu, tạo nên những “kiệt tác xanh” sống động, có khả năng biến mọi góc nhà, quán cà phê hay văn phòng thành không gian đầy cảm hứng.
                    <br><br>Điểm nổi bật tại Thuận Phát Garden là khả năng thiết kế cây kiểng theo yêu cầu riêng: từ bonsai để bàn mini đến tiểu cảnh sân vườn, từ cây phong thủy tặng khai trương đến không gian thiền tại spa, resort. Mỗi sản phẩm đều mang dấu ấn riêng của người đặt – cá nhân hóa từ kiểu dáng, thông điệp, đến cả chất liệu chậu cây hoặc vật liệu trang trí đi kèm.`,
                    img: "img__tintuc/13a.jpg"
                },
                {
                    content: `Không chỉ đẹp một cách “trưng bày”, cây kiểng của Thuận Phát còn mang tính địa phương đậm nét: mộc mạc, nhẹ nhàng và thoáng đãng như chính con người và khí hậu miền Tây. Nhiều sản phẩm lấy cảm hứng từ đồng ruộng, dòng sông, chợ nổi và nhịp sống hiền hòa của người Cần Thơ – khiến cây không chỉ là vật trang trí, mà còn là câu chuyện văn hóa thu nhỏ.`,
                    img: "img__tintuc/13b.jpg"
                },
            ],
        },
    },
    {
        id: 14,
        tieude: "🌱 Chất lượng sản phẩm đảm bảo – Từ vườn đến tay khách",
        image: "img__tintuc/14.jpg",
        url: "detail.php?id=14",
        noidung:
        {
            content1: `Tại Thuận Phát Garden, chúng tôi luôn tin rằng một sản phẩm đẹp trước hết phải là một sản phẩm chất lượng. Vì vậy, từ khâu chọn giống đến chăm sóc và giao cây cho khách, mọi công đoạn đều được kiểm soát kỹ lưỡng, đặt chữ “tâm” lên hàng đầu.

<br>📌 Từ khâu chọn cây
<br>Chúng tôi chỉ lựa chọn những cây khỏe mạnh, có nguồn gốc rõ ràng, rễ phát triển tốt và không có dấu hiệu sâu bệnh. Đối với những loại cây kiểng đặc biệt như bonsai, cây phong thủy hay terrarium, việc chọn phôi (gốc cây ban đầu) cũng được thực hiện kỹ càng để đảm bảo cây có dáng đẹp và tiềm năng phát triển ổn định.

<br>📌 Quy trình chăm sóc nghiêm ngặt
<br>Sau khi chọn cây, toàn bộ quá trình trồng, uốn, tạo thế và chăm sóc đều diễn ra trong môi trường được kiểm soát. Đất trồng là loại đất sạch đã qua xử lý, phân bón sử dụng là phân hữu cơ tự nhiên, an toàn và thân thiện với môi trường. Cây được theo dõi định kỳ để kịp thời xử lý nếu có vấn đề về sâu bệnh, thiếu chất hay môi trường không phù hợp.`,
            content__box: [
                {
                    content: `
                    📦 Kiểm tra trước khi giao hàng
<br>Trước khi đến tay khách hàng, mỗi sản phẩm đều được kiểm tra lần cuối: xem lá có bị vàng không, gốc có chắc không, cây có mọc đều và cân đối không. Chúng tôi cũng đảm bảo cây không bị va đập, đổ nghiêng hay rụng đất trong quá trình vận chuyển.

<br> 📝 Cam kết hậu mãi
<br>Khi mua cây tại Thuận Phát Garden, khách hàng sẽ được nhận hướng dẫn chăm sóc chi tiết, dễ hiểu và phù hợp với từng loại cây. Trong vòng 3–5 ngày sau khi nhận hàng, nếu cây có vấn đề do vận chuyển hoặc không đúng mô tả, chúng tôi sẽ hỗ trợ đổi cây mới hoặc xử lý phù hợp tùy tình huống. Điều quan trọng nhất là khách hàng cảm thấy yên tâm và hài lòng với sản phẩm mình đã chọn.
                    `,
                    img: "img__tintuc/14a.jpg"
                },
                {
                    content: `💚 Chúng tôi không chỉ bán cây, chúng tôi gửi gắm tâm huyết
<br>Với Thuận Phát Garden, mỗi cây kiểng không chỉ là một món hàng. Đó là công sức, thời gian và sự tận tụy của cả một quá trình nuôi trồng. Bởi vậy, sự hài lòng của khách hàng chính là minh chứng rõ nhất cho chất lượng mà chúng tôi theo đuổi.`,
                    img: "img__tintuc/14b.jpg"
                },
            ],
        },
    },
    {
        id: 15,
        tieude: "Cây Cảnh: Tinh Hoa Của Thiên Nhiên Trong Không Gian Sống",
        image: "img__tintuc/15.jpg",
        url: "detail.php?id=15",
        noidung:
        {
            content1: `💸 Giá cả hợp lý – Phù hợp với người Cần Thơ
<br>Tại Thuận Phát Garden, chúng tôi hiểu rằng mỗi người yêu cây đều có mong muốn sở hữu một không gian xanh cho riêng mình – dù là một chậu cây để bàn nhỏ xinh hay một tiểu cảnh bonsai đầy nghệ thuật. Vì vậy, tất cả sản phẩm của chúng tôi đều được niêm yết giá rõ ràng, hợp lý, phù hợp với túi tiền của nhiều đối tượng khách hàng, từ sinh viên, nhân viên văn phòng đến các anh chị yêu cây lâu năm.

<br>Với lợi thế vườn trồng và thiết kế tại Cần Thơ, chúng tôi chủ động được về nguồn cây, chi phí vận hành thấp hơn so với các đơn vị ở TP.HCM hay Hà Nội, nhờ đó giá thành luôn ở mức dễ tiếp cận nhưng chất lượng vẫn đảm bảo.

<br>Chúng tôi có nhiều phân khúc sản phẩm:

<br>🌱 Cây mini để bàn: chỉ từ 30.000đ – 100.000đ

<br>🪴 Terrarium – bình thủy tinh rêu phong: từ 150.000đ – 500.000đ

<br>🌳 Bonsai, tiểu cảnh sân vườn: từ vài trăm nghìn đến vài triệu tùy kích thước, thế cây

<br>Dù là mức giá nào, mỗi sản phẩm đều được chăm sóc kỹ lưỡng, đóng gói cẩn thận, đi kèm hướng dẫn chăm cây và chính sách hỗ trợ nếu có vấn đề sau khi nhận hàng.`,
            content__box: [
                {
                    content: `
                    🛒 Mua cây ở Thuận Phát Garden là mua trực tiếp tại vườn
<br>Không qua trung gian, không mất phí vận chuyển xa, không đội giá. Khách hàng tại Cần Thơ hoặc các khu vực lân cận có thể:

<br>Ghé trực tiếp vườn chọn cây theo ý thích

<br>Đặt hàng online và nhận cây trong ngày

<br>Được tư vấn miễn phí về không gian, phong thủy và cách chăm sóc

<br>Chúng tôi cũng thường xuyên có chương trình giảm giá, combo ưu đãi dành riêng cho khách nội thành Cần Thơ, đặc biệt vào các dịp lễ Tết, ngày Nhà giáo Việt Nam, lễ khai trương, tân gia…
                    `,
                    img: "img__tintuc/15a.jpg"
                },
                {
                    content: `
<br>💬 “Chất lượng thật – Giá đúng – Dành cho người thật sự yêu cây”
<br>Tại Thuận Phát Garden, mỗi khách hàng đều được xem là người bạn đồng hành cùng thiên nhiên. Dù bạn chỉ mua một cây nhỏ hay đầu tư một tiểu cảnh lớn, chúng tôi đều đối đãi bằng sự tận tâm và trân trọng như nhau.
                    `,
                    img: "img__tintuc/15b.jpg"
                },
            ],
        },
    },
    {
        id: 16,
        tieude: "🌿 Cây Cảnh Trồng Trong Nhà: Vừa Đẹp Vừa Tốt Cho Sức Khỏe",
        image: "img__tintuc/16.jpg",
        url: "detail.php?id=16",
        noidung:
        {
            content1: `Lợi Ích Của Việc Trồng Cây Cảnh Trong Nhà
<br>Trồng cây cảnh trong nhà không chỉ giúp không gian sống trở nên xanh mát, mà còn mang lại nhiều lợi ích thiết thực:

<br>Lọc sạch không khí: Nhiều loại cây như lưỡi hổ, lan ý, trầu bà có khả năng hấp thụ các chất độc hại như formaldehyde, benzene, và toluene, giúp không khí trong lành hơn .

<br>Tăng độ ẩm tự nhiên: Cây cảnh giúp cân bằng độ ẩm trong không khí, đặc biệt hữu ích trong môi trường điều hòa hoặc mùa khô.

<br>Giảm căng thẳng, stress: Màu xanh của cây giúp thư giãn tinh thần, cải thiện tâm trạng và tăng năng suất làm việc.

<br>Ý nghĩa phong thủy: Nhiều loại cây như kim tiền, phát tài, phú quý được cho là mang lại tài lộc và may mắn cho gia chủ`,
            content__box: [
                {
                    content: `2. Top Các Loại Cây Cảnh Trồng Trong Nhà Phổ Biến
<br>Lưỡi hổ (Sansevieria): Có khả năng hấp thụ CO₂ và thải ra oxy vào ban đêm, thích hợp đặt trong phòng ngủ.

<br>Lan ý (Spathiphyllum): Khả năng lọc formaldehyde và benzene, dễ chăm sóc, phù hợp với không gian thiếu ánh sáng.


<br>Trầu bà (Epipremnum aureum): Dễ trồng, có thể trồng thủy sinh, giúp loại bỏ khí độc trong không khí.


<br>Cây kim tiền: Mang lại may mắn, tài lộc, dễ chăm sóc và phát triển tốt trong môi trường thiếu sáng.

<br>Cây phát tài: Thân cứng, lá mọc thành chùm, biểu tượng cho sự thịnh vượng và thành công.`,
                    img: "img__tintuc/16a.jpg"
                },
                {
                    content: `3. Lưu Ý Khi Chăm Sóc Cây Cảnh Trong Nhà
<br>Ánh sáng: Chọn cây phù hợp với điều kiện ánh sáng trong nhà, tránh để cây tiếp xúc trực tiếp với ánh nắng mặt trời quá lâu.

<br>Tưới nước: Tưới nước đều đặn, tránh để đất quá ẩm ướt hoặc quá khô.

<br>Nhiệt độ: Giữ nhiệt độ trong phòng ổn định, tránh thay đổi đột ngột ảnh hưởng đến sự phát triển của cây.

<br>Bón phân: Bón phân định kỳ để cung cấp dưỡng chất cho cây, giúp cây phát triển khỏe mạnh.

`,
                    img: "img__tintuc/16b.jpg"
                },
            ],
        },
    }


]
const like = document.getElementById("like");
like.addEventListener("click", function () {
    if (like.classList.contains("like__class")) {
        like.classList.remove("like__class");
        like.classList.add("not__like__class");
        localStorage.setItem(`yeuthich${articleId - 1}`, 1);
    }
    else {
        like.classList.remove("not__like__class");
        like.classList.add("like__class");
        localStorage.setItem(`yeuthich${articleId - 1}`, 0);
    }
});
function displayLike() {
    if (localStorage.getItem(`yeuthich${articleId - 1}`) === "0") {
        like.classList.add("like__class");
    }
    else if (localStorage.getItem(`yeuthich${articleId - 1}`) === "1") {
        like.classList.add("not__like__class");
    }
    else {
        localStorage.setItem(`yeuthich${articleId - 1}`, 1);
        like.classList.add("not__like__class");
    }
};
displayLike();
function loadPage(id) {
    const detail = tintuc.find(a => a.id === parseInt(id));
    if (!detail) {
        window.location.replace("tintuc.php");
        return;
    }

    document.getElementById('title').textContent = detail.tieude;
    document.getElementById('img').src = detail.image;
    document.getElementById('content').innerHTML = detail.noidung.content1;

    const n = detail.noidung.content__box.length;
    for (let i = 0; i < n; i++) {
        const image = document.createElement("img");
        image.src = detail.noidung.content__box[i].img;
        image.id = `img${i}`;
        image.alt = "Hinh anh"
        image.classList.add("imgs");
        const content = document.createElement("p");

        content.innerHTML = detail.noidung.content__box[i].content;
        content.id = `content${i}`;
        content.classList.add("contents");
        document.getElementById("thechua").appendChild(image);
        document.getElementById("thechua").appendChild(content);
    }
}
loadPage(articleId);
//tin tuc lien quan
const lq__box = document.getElementById("tintuclienquan__container");
function displaylq() {
    lq__box.innerHTML = "";
    // console.log(tintuc);
    for (let j = 1; j <= 10; j++) {
        let item = tintuc[Math.floor(Math.random() * tintuc.length)];

        const card = document.createElement("a");
        const img = document.createElement("img");
        const title = document.createElement("p");
        card.classList.add("lienquan__card");
        title.classList.add("lienquan__title");
        img.classList.add("lienquan__img");

        card.href = item.url;
        img.src = item.image;
        title.textContent = item.tieude;

        // card.appendChild()
        card.appendChild(img);
        card.appendChild(title);
        lq__box.appendChild(card);
    }
}
displaylq();
//input 
function getFormForm() {
    var user = document.querySelector("#name");
    var comment__content = document.querySelector("#comment__content");
    var parent = user.parentElement;

    function showError(input) {
        var parent = input.parentElement;
        var noti = parent.querySelector("span");
        noti.classList.add("control-noti--error");
        noti.innerHTML = "Không để trống";
    }
    function showSucces(input) {
        var parent = input.parentElement;
        var noti = parent.querySelector("span");
        noti.classList.remove("control-noti--error");
        noti.innerHTML = "";
    }
    // showError(comment__content);
    function checkLength(input) {
        input.value = input.value.trim();
        if (input.value.length === 0) {
            showError(input);
            return true;
        }
        return false;
    }
    document.querySelector("#comment__form").addEventListener("submit", function (e) {
        e.preventDefault();
        showSucces(user);
        showSucces(comment__content);
        var checkLengthUser = checkLength(user);
        var checkLengthComment = checkLength(comment__content);
        if (!checkLengthComment && !checkLengthUser) {
            getComment();
            this.reset();
        }
    })
}
getFormForm();
document.querySelector("#btn__themmoi").addEventListener("click", function () {
    const form = document.querySelector("#comment__form");
    if (form.classList.contains("class__open__form")) {
        form.classList.remove("class__open__form");
    }
    else
        form.classList.add("class__open__form");

});
document.querySelector("#form-submit").addEventListener("click", function () {
    const form = document.querySelector("#comment__form");
    if (form.classList.contains("class__open__form")) {
        form.classList.remove("class__open__form");
    }
    else
        form.classList.add("class__open__form");
});
document.addEventListener("keydown", function (e) {
    if (e.ctrlKey && e.key === "Enter") {
        getFormForm();
    }
})




//binh luan beta    
const page = JSON.parse(localStorage.getItem(`comment${articleId}`)); //lay ra ojbect co luu 
const trash = JSON.parse(localStorage.getItem(`array${articleId}`));

// resetComment(page);     
function resetComment(page) {
    for (let i = 1; i <= tintuc.length; i++) {
        let n = 0;
        let trash = [];
        const comment = {
            id: i,
            sl: n,
            soluong: n,
            comments: [
                {
                    user: "",
                    write: "",
                    code: `${i}${n}`,
                    status_like: 0,
                    // avt: `fa-user`,
                }
            ]
        }
           localStorage.setItem(`array${i}`, JSON.stringify(trash));
        localStorage.setItem(`comment${i}`, JSON.stringify(comment));
    }
    loadComments(page, trash);
}
function getComment(e) {
    const comment = document.getElementById("comment__content").value;
    const nguoidung = document.getElementById("name").value;
  
    page.sl++;
    page.soluong++;
    page.comments[page.sl] = {
        user: nguoidung,
        write: comment,
        code: `${articleId}${page.sl}`,
        status_like: 0,
        avt: e,

    };
    
    localStorage.setItem(`comment${articleId}`,JSON.stringify(page));
    loadComments(page, trash);
    
};

function DisplaylikeComment(i){
    // event.preventDefault();
    if(page.comments[i].status_like==1){
        page.comments[i].status_like = 0;
        notification("Đã bỏ thích bình luận")
    }
    else
    {
        notification("Đã thích bình luận")
        page.comments[i].status_like=1;
    }

    localStorage.setItem(`comment${articleId}`,JSON.stringify(page));
    loadComments(page,trash)
}
function notification(message) {
        const elements = document.getElementById("notification");
    elements.textContent = message;
    elements.classList.add("notification_active");
    setTimeout(function () {
        elements.classList.remove("notification_active");
    },800);
    
}
function loadComments(page, trash) {
    const comment__box = document.getElementById("comment__box__id");
    if (page.soluong < 1) {
        comment__box.innerHTML = "Chưa tồn tại bình luận!"

    }
    else {
        comment__box.innerHTML = "";
        for (let i = 1; i <= page.sl; i++) {
            const box = document.createElement("div");
            box.classList.add("comment__bl");
            box.innerHTML = `
            
            
            <div class="name__user__box"><i class="fa-solid fa-user"></i> User name: ${page.comments[i].user}</div>
            <div class="comment__user">Nội Dung: ${page.comments[i].write}</div>
            <i class="fa-trash icon--trash fa-solid" id = "${page.comments[i].code}" onclick="deleteComment(id)"></i>
            
            <i class="fa-solid fa-thumbs-up" style="margin-left: 8px" id="like${page.comments[i].code}" onclick="DisplaylikeComment(${i})"></i>

            `;
          
  
                if (!trash.includes(`${page.comments[i].code}`)) {

                    comment__box.prepend(box);
            
            let element = document.querySelector(`#like${page.comments[i].code}`)
            if(page.comments[i].status_like==1  )
             {
                   element.classList.add("like_cmt");


             }
            else{
                element.classList.remove("like_cmt");

            }
      
                }
        }
    }
}

    function deleteComment(e) {
        trash.push(e);
        page.soluong--;
        localStorage.setItem(`array${articleId}`, JSON.stringify(trash));
        notification("Đã xóa bình luận");
        loadComments(page, trash);
    }
    loadComments(page, trash);
    // const box_icon = document.querySelector("#daidien_space");
    // box_icon.innerHTML =
    // `   
    // <i class="fa-solid fa-user-doctor avt_icon" id="fa-user-doctor" onclick="getIcon(this)" ></i>
    // <i class="fa-solid fa-hippo avt_icon" id="fa-hippo" onclick="getIcon(this.id)"></i>
    
    // <i class="fa-solid fa-dog avt_icon" id="fa-dog" onclick="getIcon(this.id)"></i>
    // <i class="fa-solid fa-cat avt_icon" id="fa-cat" onclick="getIcon(this.id)"></i>
    // <i class="fa-solid fa-face-angry avt_icon" id="fa-face-angry" onclick="getIcon(this.id)"></i>
    // `
    // function displayChooseDD(){
    //     const space = document.querySelector("#daidien_space");
    //     if(space.classList.contains("daidien_space_dispplay")){
    //         space.classList.remove("daidien_space_dispplay");
    //     }
    //     space.classList.add("daidien_space_dispplay");
    // }
    // function getIcon(e){
    //     return e;
    // }
  

