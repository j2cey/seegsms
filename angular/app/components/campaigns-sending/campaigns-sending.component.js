class CampaignsSendingController{
    constructor(){
        'ngInject';

        /*this.groups = [
            {
                title: "Dynamic Group Header - 1",
                items: [{"item-title": "item 1"}, {"item-title": "item 2"}]
            },
            {
                title: "Dynamic Group Header - 2",
                items: [{"item-title": "item 3"}, {"item-title": "item 4"}]
            }
        ];*/

        this.oneAtATime = true;

        /*this.groups = [
            {
                title: 'title 1',
                content: 'content 1',
                isOpen: true,
                list: ['<i>item1a</i> blah blah',
                    'item2a',
                    'item3a']
            },
            {
                title: 'title 2',
                content: 'Content 2',
                list: ['item1b',
                    '<b>item2b </b> blah ',
                    'item3b']
            },
            {
                title: 'title 3',
                content: 'Content 3'
            },
            {
                title: 'title 4',
                content: 'content 4'
            },
            {
                title: 'title 5',
                content: 'content 5'
            }
        ];*/

        this.groups = [
            {
                name: 'group1',
                icon: 'library',
                text: 'Books',
                body: 'When replacing a multi-lined selection of text, the generated dummy text maintains the amount of lines. When replacing a selection of text within a single line, the amount of words is roughly being maintained.'
            },
            {
                name: 'group2',
                icon: 'album',
                text: 'Music',
                body: 'When the replaced text selection consists fully of lower-cased or capital letters or begins with a capital letter, that previous casing is maintained. Furthermore, the presence or absence of a trailing punctuation mark of a replaced text selection is being maintained.'
            },
            {
                name: 'group3',
                icon: 'star-empty',
                text: 'Favorites',
                body: 'The plugin adds a random text generator, capable of creating witty texts in different genres. Created text can be inserted newly at the caret, or replace a selection. The dummy text generator is added to the main menu, tools menu and into the generate... popup (Alt+Insert).'
            }
        ];

        this.accordianData = [
            {
                "heading" : "HOLDEN",
                "content" : "GM Holden Ltd, commonly known as Holden, is an Australian automaker that operates in Australasia and is headquartered in Port Melbourne, Victoria. The company was founded in 1856 as a saddlery manufacturer in South Australia."
            },
            {
                "heading" : "FORD",
                "content" : "The Ford Motor Company (commonly referred to as simply Ford) is an American multinational automaker headquartered in Dearborn, Michigan, a suburb of Detroit. It was founded by Henry Ford and incorporated on June 16, 1903."
            },
            {
                "heading" : "TOYOTA",
                "content" : "Toyota Motor Corporation is a Japanese automotive manufacturer which was founded by Kiichiro Toyoda in 1937 as a spinoff from his father's company Toyota Industries, which is currently headquartered in Toyota, Aichi Prefecture, Japan."
            }
        ];

        this.campaignsending = [
            {
                "heading" : "Campaign 1",
                "content" : [
                    {
                        "planningId" : "1",
                        "processrate" : 19,
                        "detail" : "gfgf dvdgdgf gvgvdgdfg."
                    },
                    {
                        "planningId" : "2",
                        "processrate" : 70,
                        "detail" : "hbfhfh  hvsvss hdvdsvsdvyefjyzdg uggbhdgfdh ."
                    },
                    {
                        "planningId" : "3",
                        "processrate" : 3,
                        "detail" : "gfv fs hdhziz jdsjvdj dhvvd dvehgvgv."
                    }
                ]
            },
            {
                "heading" : "Campaign 2",
                "content" : [
                    {
                        "planningId" : "4",
                        "processrate" : 1,
                        "detail" : "hghfbh hvgfvvef ghghg de."
                    },
                    {
                        "planningId" : "5",
                        "processrate" : 45,
                        "detail" : "dcd ass tsfh sats sfssfds gtadststd."
                    }
                ]
            },
            {
                "heading" : "Campaign 3",
                "content" : [
                    {
                        "planningId" : "6",
                        "processrate" : 1,
                        "detail" : "gff hvf sfv fssdvgsdf sgd sts sdf sdsfsgd sd ddsgs stdfs y."
                    },
                    {
                        "planningId" : "7",
                        "processrate" : 81,
                        "detail" : " hdghfh  ydd fhyf shfsfs fdf."
                    },
                    {
                        "planningId" : "8",
                        "processrate" : 3,
                        "detail" : "gfv fs hdhziz jdsjvdj dhvvd dvehgvgv."
                    }
                ]
            }
        ];
    }

    $onInit(){
    }

    /*collapseAll(data) {
        for(var i in this.accordianData) {
            if(this.accordianData[i] != data) {
                this.accordianData[i].expanded = false;
            }
        }
        data.expanded = !data.expanded;
    };*/
}

export const CampaignsSendingComponent = {
    templateUrl: './views/app/components/campaigns-sending/campaigns-sending.component.html',
    controller: CampaignsSendingController,
    controllerAs: 'vm',
    bindings: {}
}


