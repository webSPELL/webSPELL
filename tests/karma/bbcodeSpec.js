describe("bbcode", function() {
    var word = "webSPELL",
        sentence = "webSPELL is a free Content Management System which was especially developed for the needs of esport related communities. Since a short while we are also offering an optimized and enhanced webSPELL version for the special requirements of non-profit organisations.";

    it("should be a valid BBCode", function() {
        expect(validbbcode("[B]" + word + "[/B]")).toBe(true);
        expect(validbbcode("[I]" + word + "[/I]")).toBe(true);
        expect(validbbcode("[U]" + word + "[/U]")).toBe(true);
        expect(validbbcode("[S]" + word + "[/S]")).toBe(true);
        expect(validbbcode("[CODE]" + word + "[/CODE]")).toBe(true);
        expect(validbbcode("[LIST]" + word + "[/LIST]")).toBe(true);
        expect(validbbcode("[EMAIL=]root@webspell.org[/EMAIL]")).toBe(true);
        expect(validbbcode("[URL]http://www.webspell.org[/URL]")).toBe(true);
        expect(validbbcode("[URL=http://www.webspell.org]webSPELL[/URL]")).toBe(true);
        expect(validbbcode("[IMG]http://www.webspell.org/images/avatars/1.png[/IMG]")).toBe(true);
        expect(validbbcode("[QUOTE]" + sentence + "[/QUOTE]")).toBe(true);
        expect(validbbcode("[TOGGLE="+ word +"]" + sentence + "[/TOGGLE]")).toBe(true);
        expect(validbbcode("[SIZE=3]" + sentence + "[/SIZE]")).toBe(true);
        expect(validbbcode("[COLOR=red]" + sentence + "[/COLOR]")).toBe(true);
        expect(validbbcode("[COLOR=#FF0000]" + sentence + "[/COLOR]")).toBe(true);
        expect(validbbcode("[ALIGN=START]" + sentence + "[/ALIGN]")).toBe(true);
        expect(validbbcode("[ALIGN=END]" + sentence + "[/ALIGN]")).toBe(true);
        expect(validbbcode("[ALIGN=LEFT]" + sentence + "[/ALIGN]")).toBe(true);
        expect(validbbcode("[ALIGN=RIGHT]" + sentence + "[/ALIGN]")).toBe(true);
        expect(validbbcode("[ALIGN=CENTER]" + sentence + "[/ALIGN]")).toBe(true);
        expect(validbbcode("[ALIGN=JUSTIFY]" + sentence + "[/ALIGN]")).toBe(true);
        expect(validbbcode("[FONT=ARIAL]" + sentence + "[/FONT]")).toBe(true);
    });

    it("should be a invalid BBCode", function() {
        // expect(validbbcode("[FOOBAR]Not Existing[/BARFOO]")).toBe(false);
        expect(validbbcode("[U][B]" + word + "[/U][/B]")).toBe(false);
        expect(validbbcode("[U]" + word + "")).toBe(false);
        expect(validbbcode("" + word + "[/U]")).toBe(false);
    });
});
