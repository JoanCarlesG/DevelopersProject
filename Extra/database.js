db.createCollection("user")
db.insertOne(
    {
        user: {
            user_id: 1,
            name: "UserName",
            tasks: [
                {
                    task_id: 1,
                    title: "TaskTitle 1",
                    desc: "Task description 1",
                    status: "todo",
                    start_date: Date(),
                    mod_date: Date(),
                    end_date: Date()
                },
                {
                    task_id: 2,
                    title: "TaskTitle 2",
                    desc: "Task description 2",
                    status: "doing",
                    start_date: Date(),
                    mod_date: Date(),
                    end_date: Date()
                }
            ]

        }
    }
    
)
